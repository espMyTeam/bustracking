#include <SPI.h>
#include <Ethernet.h>
#include <TinyGPS++.h>
#include <SoftwareSerial.h>


// **********************parametres du shield ethernet
IPAddress ip(192,168,1,200);
EthernetClient client;
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
char server [] = "bustracking-kamsapp.rhcloud.com";

// **********************parametres du shield gps
TinyGPSPlus gps; 
SoftwareSerial SoftSerial(2, 3);
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




void setup(){

  //initialiser la voie série
  Serial.begin(9600);

  //initialiser le shield ethernet
  initEthernet();
  wait(1000);

  //initialiser le shield gps
  initGPS();
  wait(1000);
  

  //testes
  //sendEthernetData("bus 10111dk ligne 10 14.681384 -17.466691 16.4000 2.037200 2 11502200 260416 783893435");
  
}

void loop(){
   if(SoftSerial.available() > 0)
      if(gps.encode(SoftSerial.read())){
        gps_state=1; 
        //Serial.println("reception donnees satellite...");
      }
      
  if(((unsigned long)(millis() - timer >= dt) || depart==1)&&(gps_state==1)){
      tracking();
      timer = millis();
    }
}

void initEthernet(){
  Serial.println("Intialisation du shield ethernet");
  //initialiser avec DHCP ou avec un ip fixe sinon
  if(!Ethernet.begin(mac)){
    Ethernet.begin(mac, ip);
    Serial.print("parametrage avec ip fixe: ");
    Serial.println(ip);
  }else{
    Serial.println("parametrage avec dhcp");
  }
}

void initGPS(){
  Serial.println("Intialisation du shield GPS");
  SoftSerial.begin(9600);  // the SoftSerial baud rate 
  pinMode(onModulePin, OUTPUT);
  digitalWrite(onModulePin,HIGH); 
  wait(100);
  digitalWrite(onModulePin,LOW);
  sendEthernetData("Synchronisation du bus");
}



void sendEthernetData(String donnees){

  //formatter les donnees
  donnees +=" 783893435";
  donnees.replace(" ", "%20");
  
  //se connecter
  client.connect(server, 80);
  Serial.println("Connexion...");

  //envoyer les donnees
  Serial.println("envoi des donnees:\n<<");
  Serial.println(donnees);
  Serial.println(">>");
  
  client.print("GET /remote_request.php?donnees=");
  client.print(donnees);
  client.print("&num=783893435");
  client.println(" HTTP/1.1");
  client.print("Host: ");
  client.println(server);
  client.println("Connection: keep-alive");
  client.println();

   //se deconnecter
  Serial.println("Deconnexion...");
  client.stop();
}

/* envoie des corrdonnees gps */
void sendGpsEthernetData(){
  
  //se connecter
  client.connect(server, 80);
  Serial.println("Connexion...");

  //envoyer les donnees
  Serial.println("envoi des donnees:\n<<");
 /* Serial.println(donnees);*/
 
  
  client.print("GET /remote_request.php?donnees=");

  client.print("bus"); //matricule
  client.print("%20");
  client.print(matricule_bus); //matricule
  client.print("%20");
  client.print("ligne");
  client.print("%20");
  client.print(ligne); //ligne
  client.print("%20");
  client.print(last_lat, 6); //Recuperation et ecriture de la latitude actuelle sur le SMS
  client.print("%20");
  client.print(last_lng, 6); //Recuperation et ecriture de la longitude actuelle sur le SMS
  client.print("%20");
  client.print(last_alt, 4); 
  client.print("%20");
  client.print(gps.speed.kmph(), 6); //Recuperation et ecriture de la vitesse actuelle sur le SMS
  client.print("%20");
  client.print("2");
  client.print("%20");
  client.print(gps.time.value()); //Recuperation et ecriture de l'heure actuelle sur le SMS
  client.print("%20");
  client.print(gps.date.value()); //Recuperation et ecriture de la date actuelle sur le SMS
  client.print("%20");
  client.print("783893435");

  Serial.println(">>");
  
  client.print("&num=783893435");
  client.println(" HTTP/1.1");
  client.print("Host: ");
  client.println(server);
  client.println("Connection: keep-alive");
  client.println();
  

   //se deconnecter
  Serial.println("Deconnexion...");
  client.stop();
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
        sendGpsEthernetData();
        distance = 0;
      }

      if(depart == 1){ //on envoie si c'est le départ
        Serial.println("premiere envoie des cordonneers gps");
        sendGpsEthernetData();
        depart = 0; 
      }
    
   }
}

