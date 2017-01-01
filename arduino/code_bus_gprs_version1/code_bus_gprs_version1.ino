#include <TinyGPS++.h>
#include <SoftwareSerial.h>

// **********************parametres du shield gps
TinyGPSPlus gps; 
SoftwareSerial SoftSerial(2, 3);

//******************** gprs
SoftwareSerial gprs(6, 7);

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
  
    delay(500);
    
}

void loop()
{
  if(SoftSerial.available() > 0)
      if(gps.encode(SoftSerial.read())){
        gps_state=1; 
        Serial.println("reception donnees satellite...");
      }
      
  if(((unsigned long)(millis() - timer >= dt) || depart==1)&&(gps_state==1)){
      tracking();
      timer = millis();
    }
}


void initGPRS(){
  Serial.println("Intialisation du shield GSM/GPRS");
  gprs.begin(9600);
  pinMode(9, OUTPUT);
  digitalWrite(9,HIGH);
  wait(1000);
  digitalWrite(9,LOW);
  wait(2000);
  sendGPRSData("Demarrage gprs");
  
}

void initGPS(){
  Serial.println("Intialisation du shield GPS");
  SoftSerial.begin(9600);  // the SoftSerial baud rate 
  pinMode(onModulePin, OUTPUT);
  digitalWrite(onModulePin,HIGH); 
  wait(100);
  digitalWrite(onModulePin,LOW);
  sendGPRSData("Synchronisation du bus");
}


void sendGPRSData(String data)
{
    Serial.println(data);
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

    gprs.print("AT+HTTPPARA=\"URL\",\"bustracking-kamsapp.rhcloud.com/remote_request.php?donnees=" + data + " 783893435&num=783893435\"");
    delay(1000);

    ShowSerialData();

    gprs.println("AT+HTTPACTION=0");//submit the request
    delay(10000);

    ShowSerialData();

    gprs.println("AT+HTTPREAD");// read the data from the website you access
    delay(300);

    ShowSerialData();

    gprs.println("");
    delay(100);
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
//Serial.println(cur_lat);
  
  if(gps.location.isUpdated()){

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
        //sendGpsGPRSData();
        distance = 0;
      }

      if(depart == 1){ //on envoie si c'est le départ
        Serial.println("premiere envoie des cordonneers gps");
       // sendGpsGPRSData();
        depart = 0; 
      }
    
   }
}

