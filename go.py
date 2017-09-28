#!/usr/bin/python

# definisco i parametri per la connessione al database
import json
config = json.load(open("config.json"))

DBuser = config["database"]["user"]
DBpass = config["database"]["pass"]
DBhost = config["database"]["host"]
DBname = config["database"]["name"]

# importo un po' di librerie python che mi servono
import StringIO
import mimetools
import multifile
import string
import urllib
from rfc822 import parsedate
import MySQLdb
from time import strftime
import sys


# il file con le varie mail pu essere passato come argomento allo script
if len(sys.argv)>1:
	FILE = sys.argv[1]
else:	
	FILE = '/documenti/varie/carbonara/carb'
	

# stabilisco la connessione al database (e' una variabile globale)
Conn = MySQLdb.connect(host=DBhost,user=DBuser,passwd=DBpass,db=DBname)

# creo un cursore (anche questo globale) che mi serve
# a sparare le query sql al database
RSuser = Conn.cursor()

# procedura insertintodb
# accetta in ingresso:
#	sender: il mittente del messaggio
#	date: una tupla contenente la data di invio del messaggio
# 	formdata: i dati della form in formato urlencoded
#
#	cosa fa: inserisce i dati nel database


def insertintodb(sender, date, formdata):

	# creo un dizionario che mi sara' comodo per costruire la query sql
	dict = {}

	dict["email"] = sender
	dict["date"] = strftime("%Y-%m-%d %H:%M:%S", date)

	# splitto i dati della form in coppie
	coppie = string.split(formdata,"&")
	
	# per ogni coppia chiave=valore leggo i valori
	for coppia in coppie:
		(chiave, valore) = string.split(coppia,"=")
	
		# effetto l'unquoting (i dati sono urlencoded)
		chiave = urllib.unquote_plus(chiave)
		valore = urllib.unquote_plus(valore)

		# processo il campo persone/uova che e' un po' particolare
		if chiave == "persone/uova":
			if valore == "Un uovo a testa":
				valore = "="
			elif valore == "Piu' uova che persone: per es 3/4":
				valore = "+"
			elif valore == "Meno uova che persone: per es 4/3":
				valore = "-"
			else:
				valore = "NULL"
			chiave = "persone_uova"
		
		# assegno la coppia chiave=valore al dizionario
		dict[chiave] = valore
	
	# inizio della stringa sql
	sql_beg = "insert into feedback ("
	# fine della stringa sql
	sql_end = "VALUES ("
	
	# aggiungo i dati del dizionario alla stringa sql
	for chiave in dict.keys():
		sql_beg = sql_beg + chiave + ", "
		# per i valori devo effettuare alcune sostituzioni
		# perche' per l'sql il carattere apice e' un carattere speciale
		sql_end = sql_end + "'" + string.replace(dict[chiave],"'","''") + "', "
	
	# rimuovo gli ultimi 2 caratteri dai pezzi della query
	# e aggiungo i terminatori
	sql_beg = sql_beg[:-2] + ") "
	sql_end = sql_end[:-2] + ");"

	# costruisco la query completa
	sql = sql_beg + sql_end
	# la sparo al database
	RSuser.execute(sql)
	


# procedura process
# accetta in ingresso:
#	stream: un file object 
#	i : un contatore dei messaggi (mi serve a vedere dove siamo arrivati e per debug)
# 	
#	cosa fa: processa i messaggi che possono essere assai diversi


def process(stream,i):

	# vado all'inizio del file
	stream.seek(0)
	
	# creo un'istanza di msg di posta elettronica
	msg = mimetools.Message(stream)
	# tipo di messaggio
	msgtype = msg.gettype()
	# codifica del messaggio
	encoding = msg.getencoding()
	
	# ricavo il mittente e la data
	sender = msg.get("From")
	date = parsedate(msg.get("Date"))
	
	# se il messaggio e' un multipart lo devo processare in maniera 
	# un po' particolare
	if msgtype[:10] == "multipart/":
		
		# creo un istanza multifile
		file = multifile.MultiFile(stream)
		# ricavo il boundary
		file.push(msg.getparam("boundary"))
		# scorro i vari pezzi del file
		while file.next():
			# ricavo il sottomessaggio
			submsg = mimetools.Message(file)
			try:
				# provo a decodificare il sottomessaggio
				data = StringIO.StringIO()
				mimetools.decode(file, data, submsg.getencoding())
			except ValueError:
				# in caso di errore faccio finta di niente
				print "### ERRORI DI FORMATO NEL MSG %s (SENDER: %s)" % (i, sender)
				continue
		# vado al file successivo
		file.pop()
	
	# se il messaggio e' singolo tento
	# di decodificare il singolo messaggio
	else:
		try:
			data = StringIO.StringIO()
			mimetools.decode(msg.fp, data, msg.getencoding())
		except ValueError:
			print "### CAZZI NEL DECODING MIME DEL MSG %s (SENDER: %s)" % (i, sender)
	
	# ecco che ho i dati del form depurati dalla codifica del client di posta elettronica
	form_data = data.getvalue()
	# rimuovo i ritorni a capo
	form_data = string.replace(form_data,"\n","")
	
	# inserisco i dati nel database
	insertintodb(sender, date, form_data)

	# incremento il contatore
	i = i + 1

	# torno il valore del contatore
	return i
	
	

# funzione main del programma

def main():

	# inizializzo il contatore
	i = 1
	
	# apro il file delle mail in lettura
	f = open(FILE,"r")

	# leggo la prima linea
	line = f.readline()

	# creo uno stringofile da processare come messaggio di posta
	# elettronica: ogni singolo messaggio di posta elettronica
	# verra' inserito in questa istanza Message
	Message = StringIO.StringIO()
	
	# ci stianto la prima linea del file
	Message.write(line)

	# continuo a leggere il file fino alla fine
	while line:
		line = f.readline()
		# quando la linea inizia con "From " siamo
		# in presenza di un nuovo messaggio
		if line[0:5]!="From ":
			Message.write(line)
		else:
			# processo il singolo messaggio e
			# ne creo uno nuovo
			i = process(Message,i)
			Message = StringIO.StringIO()
			Message.write(line)
	
	# chiudo il file
	f.close()
	
	# stampo il valore del contatore
	print "%s messaggi processati" % (i-1)
	
	# chiudo la connessione al database
	Conn.close()
	

# questo e' un po' difficile da spiegare
# ma serve a dire allo script cosa fare
# quando non viene importato come modulo
# ma eseguito come programma principale

if __name__=="__main__":
	
	main()

