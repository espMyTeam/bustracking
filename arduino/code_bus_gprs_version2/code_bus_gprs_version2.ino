#include <TinyGPS++.h>
#include <GPRS_Shield_Arduino.h>
#include <SoftwareSerial.h>
#include <Wire.h>
#include <AltSoftSerial.h>

// **********************parametres du shield gps
TinyGPSPlus gps; 
AltSoftSerial SoftSerial;

//******************** gprs
#define PIN_TX    6
#define PIN_RX    7
#define BAUDRATE  9600
GPRS gprs(PIN_TX, PIN_RX, BAUDRATE);

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
  
   // delay(500);
    
}

void loop()
{
//  if(SoftSerial.available() > 0)
//      if(gps.encode(SoftSerial.read())){
//        gps_state=1; 
//        //Serial.println("reception donnees satellite...");
//      }
//      
//  if(((unsigned long)(millis() - timer >= dt) || depart==1)&&(gps_state==1)){
//      tracking();
//      timer = millis();
//    }
}


void initGPRS(){
  Serial.println("Intialisation du shield GSM/GPRS");
//  gprs.begin(9600);
  while(!gprs.init()) {
      delay(1000);
      Serial.print("Erreur d'initialisation du modem GSM\r\n");
  }
  delay(3000);    
  // attempt DHCP
  while(!gprs.join(F("internet"))) {
      Serial.println("Impossible de se connecter au reseau GPRS");
      delay(2000);
  }

  // successful DHCP
  Serial.print("Connexion au GPRS reussie. \nAdresse IP:");
  Serial.println(gprs.getIPAddress());

  float lat,lng;
  gprs.getLocation(F("internet"), &lat, &lng);
  Serial.println(lat);
  Serial.println(lng);
  sendGPRSData("syncrhonisation du bus");
  
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
    char http_cmd[] = "GET /remote_request.php?donnees=data&num=773675372 HTTP/1.1\r\n\r\n";
    char buffer[512];
    if(!gprs.connect(TCP,"bustracking-kamsapp.rhcloud.com", 80)) {
      Serial.println("erreur de connexion");
    }else{
        Serial.println("connexion reussie");
    }

    Serial.println("attente d'envoie...");
    gprs.send(http_cmd, sizeof(http_cmd)-1);
    while (true) {
        int ret = gprs.recv(buffer, sizeof(buffer)-1);
        if (ret <= 0){
            Serial.println("envoie termine...");
            break; 
        }
        buffer[ret] = '\0';
        Serial.print("Recv: ");
        Serial.print(ret);
        Serial.print(" bytes: ");
        Serial.println(buffer);
    }
    gprs.close();
    gprs.disconnect();
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

