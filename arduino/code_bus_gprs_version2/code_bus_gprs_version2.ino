#include <TinyGPS++.h>
#include <AltSoftSerial.h>


// **********************parametres du shield gps
TinyGPSPlus gps; 
AltSoftSerial SoftSerial(8, 9);

//******************** gprs
AltSoftSerial gprs(2, 3);

char server [] = "bustracking-kamsapp.rhcloud.com";
int onModulePin=4;

//***********************configuration du bus
int8_t answer; 
char aux_string[30];

/******************* configuration globale *********************/

unsigned short dt = 5*1000; // =periode recuperation des coordonnées GPS
double distance_seuille = 100; //distance minimale en m entre deux 2 arrets ***
double pas_seuil = 10; /*pas minimum : si on mesure une distance plus petite pendant dt, on considère que le vehicule est en arret ie donc erreur due à la precision gps*/

byte gps_state=0;

/******************* variables utiles *********************/

unsigned short timer=millis();

//derniere position
double last_lat=0;
double last_lng=0;
double last_alt=0;

//position courante
double cur_lat=0;
double cur_lng=0;
double cur_alt=0;

double  distance=0;
unsigned short depart = 1; /*precise le depart du bus. deux valeurs possibles 1(depart) , 0 (sinon). Le depart est preciser en appuyant sur un bouton poussoir etc..*/

/*********************** configurations propores au bus *********************/

String matricule_bus = "10111dk"; //***
String ligne = "10"; //***
char sens [] = "arret16";


void setup()
{
    //le port serie
    Serial.begin(9600);

    //initialiser le gprs
    initGPRS();
    wait(1000);

    //initialiser le shield gps
    initGPS();
    wait(1000);
    
    

    sendGPRSData("Synchronisation du bus");
  
    delay(500);
    
}

void loop()
{
  //Serial.println(gps.satellites.value());
  if(SoftSerial.available() > 0)
      if(gps.encode(SoftSerial.read())){
        gps_state=1; 
        
      }
      
  if(((unsigned long)(millis() - timer >= dt) || depart==1)&&(gps_state==1)){
      tracking();
      timer = millis();
    }
}


void initGPRS(){
  Serial.println("Intialisation du shield GSM/GPRS");
  gprs.begin(9600);
  pinMode(5, OUTPUT);
  digitalWrite(5,LOW);
  wait(1000);
  digitalWrite(5,HIGH);
  wait(2000);
  
}

void initGPS(){
  Serial.println("Intialisation du shield GPS");
  SoftSerial.begin(9600);  // the SoftSerial baud rate 
  pinMode(onModulePin, OUTPUT);
  digitalWrite(onModulePin,HIGH); 
  wait(100);
  digitalWrite(onModulePin,LOW);
  wait(2000);
}


void sendGPRSData(String data)
{
    Serial.println("Envoie de donnees");
    gprs.println("AT+CSQ");
    delay(100);

    ShowSerialData();// this code is to show the data from gprs shield, in order to easily see the process of how the gprs shield submit a http request, and the following is for this purpose too.

    gprs.println("AT+CGATT?");
    delay(100);

    ShowSerialData();

    gprs.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");//setting the SAPBR, the connection type is using gprs
    delay(1000);

    ShowSerialData();

    gprs.println("AT+SAPBR=3,1,\"APN\",\"internet\"");//setting the APN, the second need you fill in your local apn server
    delay(4000);

    ShowSerialData();

    gprs.println("AT+SAPBR=1,1");//setting the SAPBR, for detail you can refer to the AT command mamual
    delay(2000);

    ShowSerialData();

    gprs.println("AT+HTTPINIT"); //init the HTTP request

    delay(2000);
    ShowSerialData();

    gprs.println("AT+HTTPPARA=\"URL\",\"bustracking-kamsapp.rhcloud.com/remote_request.php?donnees=" + data + " 783893435&num=783893435\"");
    //gprs.println("AT+HTTPPARA=\"URL\",\"bustracking-kamsapp.rhcloud.com/position_bus.php?ligne=10\"");
    delay(1000);

    ShowSerialData();

    gprs.println("AT+HTTPACTION=0");//submit the request
    delay(10000);//the delay is very important, the delay time is base on the return from the website, if the return datas are very large, the time required longer.
    //while(!gprs.available());

    ShowSerialData();

    gprs.println("AT+HTTPREAD");// read the data from the website you access
    delay(300);

    ShowSerialData();

    gprs.println("");
    delay(100);

    Serial.println("fin envoi de donnees");
}


void sendGpsGPRSData()
{
    Serial.println("Envoie de donnees");
    gprs.println("AT+CSQ");
    delay(100);

    ShowSerialData();

    gprs.println("AT+CGATT?");
    delay(100);

    ShowSerialData();

    gprs.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");
    delay(1000);

    ShowSerialData();

    gprs.println("AT+SAPBR=3,1,\"APN\",\"internet\"");
    delay(4000);

    ShowSerialData();

    gprs.println("AT+SAPBR=1,1");
    delay(2000);

    ShowSerialData();

    gprs.println("AT+HTTPINIT"); 

    delay(2000);
    ShowSerialData();

    gprs.print("AT+HTTPPARA=\"URL\",\"bustracking-kamsapp.rhcloud.com/remote_request.php?donnees=");
    gprs.print("bus"); //matricule
    gprs.print("%20");
    gprs.print(matricule_bus); //matricule
    gprs.print("%20");
    gprs.print("ligne");
    gprs.print("%20");
    gprs.print(ligne); //ligne
    gprs.print("%20");
    gprs.print(last_lat, 6); //Recuperation et ecriture de la latitude actuelle sur le SMS
    gprs.print("%20");
    gprs.print(last_lng, 6); //Recuperation et ecriture de la longitude actuelle sur le SMS
    gprs.print("%20");
    gprs.print(last_alt, 4); 
    gprs.print("%20");
    gprs.print(gps.speed.kmph(), 6); //Recuperation et ecriture de la vitesse actuelle sur le SMS
    gprs.print("%20");
    gprs.print("2");
    gprs.print("%20");
    gprs.print(gps.time.value()); //Recuperation et ecriture de l'heure actuelle sur le SMS
    gprs.print("%20");
    gprs.print(gps.date.value()); //Recuperation et ecriture de la date actuelle sur le SMS
    gprs.print("%20");
    gprs.print("783893435");
    gprs.println("&num=783893435\"");
    //gprs.println("AT+HTTPPARA=\"URL\",\"bustracking-kamsapp.rhcloud.com/position_bus.php?ligne=10\"");
    delay(1000);

    ShowSerialData();

    gprs.println("AT+HTTPACTION=0");//submit the request
    delay(10000);

    ShowSerialData();

    gprs.println("AT+HTTPREAD");
    delay(300);

    ShowSerialData();

    gprs.println("");
    delay(100);

    Serial.println("fin envoi de donnees");
}

void ShowSerialData()
{
    while(gprs.available()!=0)
    Serial.write(gprs.read());
}

static void wait(unsigned long ms){
  unsigned long start = millis();
  while (millis() - start < ms);
}

/**
 * echantillonnage
 */
void tracking(){
  String data = "";
  
  if(gps.location.isUpdated()){
      //Serial.println("tracking");
      cur_lat = gps.location.lat();
      cur_lng = gps.location.lng();
      cur_alt = gps.altitude.meters();

      unsigned int delta_distance = TinyGPSPlus::distanceBetween(last_lat, last_lng, cur_lat, cur_lng);
      //Serial.println(delta_distance);
      if(delta_distance>=pas_seuil){ 
        distance += delta_distance; 
        Serial.print("distance:");
        Serial.print(distance);
        Serial.println("m");
      }

      last_lat = cur_lat;
      last_lng = cur_lng;
      last_alt = cur_alt;

      
      
      if(distance >= distance_seuille && depart == 0){
        
        sendGpsGPRSData();
        distance = 0;
      }

      if(depart == 1){ //on envoie si c'est le départ
        Serial.println("premiere envoie des cordonneers gps");
        sendGpsGPRSData();
        depart = 0; 
      }
    
   }
}



