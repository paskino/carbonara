#!/usr/bin/python

# ALCUNI PARAMETRI DI CONFIGURAZIONE DELLO SCRIPTELLO
import json
config = json.load(open("config.json"))

# definisco i parametri per la connessione al database
DBuser = config["database"]["user"]
DBpass = config["database"]["pass"]
DBhost = config["database"]["host"]
DBname = config["database"]["name"]

# il programma deve mandare anche un msg di posta o no? 
SMTP = config["email"]["send"]
# server smtp per l'invio del msg
SMTPSERVER = config["email"]["host"]
# indirizzo di destinazione del msg
ADDRESS = config["email"]["recipient"]
# indirizzo di provenienza del msg
FROM = config["email"]["sender"]
# subject del msg
SUBJECT = config["email"]["subject"]

# url al quale sbattere l'utente dopo la compilazione del modulo
# se vuoto stampo  un msg di ringraziamentino
LOCATION = config["redirect"]

from string import replace
import cgi
import MySQLdb

def main():

	chiavi = ["identita","email","soffritto","chiara","pepe","peperoncino","persone/uova","kindaformaggio","formaggio", "padella","ricetta"]
	data = {}
	
	# lettura della form
	form = cgi.FieldStorage()
	for key in chiavi:
		try:
			valore = form[key].value
			# processo il campo persone/uova che e' un po' particolare
			if key == "persone/uova":
				if valore == "Un uovo a testa":
					valore = "="
				elif valore == "Piu' uova che persone: per es 3/4":
					valore = "+"
				elif valore == "Meno uova che persone: per es 4/3":
					valore = "-"
				else:
					valore = "NULL"
				key = "persone_uova"
			
			# assegno la coppia chiave=valore al dizionario
			data[key] = valore
			
		except:
			data[key] = ""

	
	# inserisco nel database
	sql_beg = "INSERT INTO feedback (date, "
	sql_end = "VALUES (NOW(), "
	
	for key in data.keys():
			if data[key]:
				data[key] = replace(data[key],"'","''")
				sql_beg = sql_beg + key + ", "
				sql_end = sql_end + "'" + data[key] + "', "
			
	# rimuovo gli ultimi 2 caratteri dai pezzi della query
	# e aggiungo i terminatori
	sql_beg = sql_beg[:-2] + ") "
	sql_end = sql_end[:-2] + ");"

	# costruisco la query completa
	sql = sql_beg + sql_end
	
	# apriamo la connessione al database
	Conn = MySQLdb.connect(host=DBhost,user=DBuser,passwd=DBpass,db=DBname)
	# creo un cursore che mi serve
	# a sparare le query sql al database
	RSuser = Conn.cursor()
	# sparo la query al database
	RSuser.execute(sql)
	RSUser.close()
	Conn.commit()
	Conn.close()

	# mando il msg di posta se necessario
	if SMTP:
		import smtplib
		msg = "From: %s\r\nTo: %s\r\nSubject: %s\r\n" % (FROM,ADDRESS,SUBJECT)
		msg = msg + "\r\n"
		for key in data.keys():
			msg = msg + key + ": " + data[key] + "\r\n"

		smtp = smtplib.SMTP(SMTPSERVER)
		smtp.sendmail(FROM,ADDRESS,msg)
		smtp.quit()


	# output per l'utente
	print "Content-type: text/html"
	
	if LOCATION:
		print "Location: %s" % LOCATION 	
		print 
		print """
		<HTML>
		<BODY>
		<H1>Object Moved</H1><br>
		<a href="%s">Click Here</a>
		</BODY>
		</HTML>""" % LOCATION
	else:
		print
		print """
		<HTML>
		<BODY bgcolor="#ccddff">
		<center>
		<h2>Grazie per aver partecipato al calcolo<br>
		della ricetta quadratica media della carbonara</h2>
		<br>
		<img src="images/cuoco2.gif">
		<br><b>
		<p> Vai a vedere la
		<a href="carb_query.php">ricetta quadratica media</a> della pasta alla carbonara.</b>
		</center>
		<!-- %s -->
		</BODY>
		</HTML>
		""" % sql


if __name__=="__main__":
	try:
		main()
	except:
		print "Content-type: text/html"
		print 
		cgi.print_exception()

