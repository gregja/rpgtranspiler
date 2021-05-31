# rpgtranspiler
Convert RPG code from fixed to free format

The aim of this project is to convert code written in RPG Fixed format to the RPG Free format.
This is an experimental project I created for my own convenience, because I was frustrated by the RPG converter provided by IBM on RDI.

WARNING :
Note that I don't provide any support and any warranty about the code converted in Free format.
It's your own responbability to check if the code converted works correctly, and is functionally equivalent to the original code.

To test the RPG transpiler, just launch the app in a LAMP (or WAMP) environment, and use the form on the main page to import and convert a RPG code.

You can also use the converter in your own PHP code by adapting the example below :

```php
$src_original = file_get_contents($code_path);

$conv = new CvtsrcRPG2Free($src_original, 'Your_component_name', 'PGM');
$conv->conversion();
$src_final = $conv->retrieve();

if (trim($src_final) != '') {
    file_put_contents($encoded, $src_final);
} else {
    $errors[] = 'Conversion failed';
}
```

The instructions which are not converted in RPG Free are :
- CABEQ
- GOTO
- TAG
- MOVEL

Here is an excerpt of a code written in fixed format :

```RPG
     C                   Z-ADD     0             I                 2 0
     C                   Z-ADD     0             I2                2 0
     C                   Z-ADD     0             J                 2 0
     C                   Z-ADD     0             WMONT            30 9
     C                   Z-ADD     *HIVAL        WMONT2           22 9
     C                   Z-ADD     *HIVAL        WTOTAL           17 6
     C                   MOVEL     ' '           TAG               1
      *
     C                   Z-ADD     0             ZMEMO
     C                   EXSR      INITI1
     C     TRAN01        TAG
     C                   EXFMT     FENETRE
     C     *IN03         CABEQ     *ON           FIN01
     C     *IN12         CABEQ     *ON           FIN01
      * CALCUL POSITION DU CURSEUR
     C                   EXSR      POCUR
     C     *IN04         IFEQ      *ON
     C                   EXSR      GUIDE
     C                   ENDIF
      * REPOSITIONNEMENT DU CURSEUR
     C                   Z-ADD     POSLI         NULIG
     C                   Z-ADD     POSCO         NUCOL
      * VERIFICATION TRANSACTION
     C                   MOVEL     '0'           VER01             1
     C                   EXSR      VERIF1
     C     VER01         CABEQ     '1'           TRAN01
      * CALCUL DU TOTAL
     C                   EXSR      VALID1
      * MISE EN MEMOIRE
     C     *IN05         IFEQ      *ON
     C                   Z-ADD     ZTOTAL        ZMEMO
     C                   ENDIF
      * CLEAR DE LA MEMOIRE
     C     *IN06         IFEQ      *ON
     C                   Z-ADD     0             ZMEMO
     C                   ENDIF
      * CLEAR DE TOUTES LES ZONES (SAUF LA MEMOIRE)
     C     *IN07         IFEQ      *ON
     C                   EXSR      INITI1
     C                   ENDIF
     C                   GOTO      TRAN01
     C     FIN01         TAG
      *
     C                   SETON                                        LR
```

... and the equivalent converted in Free format by the RPG tranpiler :

```RPG
     D I               S              2  0
     D I2              S              2  0
     D J               S              2  0
     D WMONT           S             30  9
     D WMONT2          S             22  9
     D WTOTAL          S             17  6
     D TAG             S              1   
     D VER01           S              1   
     D POSLI           S              3  0
     D POSCO           S              3  0
      /free
          I = 0 ;
          I2 = 0 ;
          J = 0 ;
          WMONT = 0 ;
          WMONT2 = *HIVAL ;
          WTOTAL = *HIVAL ;
          TAG = ' ' ;

          ZMEMO = 0 ;
          Exsr INITI1 ;
      /end-free
     C     TRAN01        TAG
      /free
          Exfmt FENETRE ;
      /end-free
     C     *IN03         CABEQ     *ON           FIN01
     C     *IN12         CABEQ     *ON           FIN01
      * CALCUL POSITION DU CURSEUR
      /free
          Exsr POCUR ;
          If ( *IN04 = *ON ) ;
          Exsr GUIDE ;
          Endif ;
          // REPOSITIONNEMENT DU CURSEUR
          NULIG = POSLI ;
          NUCOL = POSCO ;
          // VERIFICATION TRANSACTION
          VER01 = '0' ;
          Exsr VERIF1 ;
      /end-free
     C     VER01         CABEQ     '1'           TRAN01
      * CALCUL DU TOTAL
      /free
          Exsr VALID1 ;
          // MISE EN MEMOIRE
          If ( *IN05 = *ON ) ;
          ZMEMO = ZTOTAL ;
          Endif ;
          // CLEAR DE LA MEMOIRE
          If ( *IN06 = *ON ) ;
          ZMEMO = 0 ;
          Endif ;
          // CLEAR DE TOUTES LES ZONES (SAUF LA MEMOIRE)
          If ( *IN07 = *ON ) ;
          Exsr INITI1 ;
          Endif ;
      /end-free
     C                   GOTO      TRAN01
     C     FIN01         TAG
      *
      /free
          *INLR = *ON ;
```


