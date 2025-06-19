<img src="/images/blog/1.webp" width="800" />

<br>
<br>

## 🧠 Introduction

Réalisation du CTF **COMCYBER X ROOT-ME PRO**, 6 flags à trouver du **2 septembre jusqu’au 30 septembre 2024** (4 jours pour moi, je suis arrivé légèrement en retard 😬). Un bon moyen de se tester !

Le challenge prévoit de mettre nos compétences en cybersécurité à l’épreuve à travers une série de défis stimulants dans les domaines du **web, forensic, crypto, reverse, stéganographie et réseau**. 🌐

<br>
<br>

## 🙋‍♂️ Présentation

Je suis **Sandro Marco**, étudiant à l’école **2600**. Passionné par l’informatique depuis toujours, je m’oriente sérieusement vers la cybersécurité depuis un an.

J’ai tout appris en autodidacte et je suis heureux d’intégrer l’école 2600, qui est une école 100 % cyber.

> **PS :** Je suis toujours à la recherche d’une alternance 😊

<br>
<br>

## 🔍 Le Challenge

Après une rapide inscription au challenge, nous arrivons sur une page qui nous explique le contexte et propose de télécharger les **logs réseaux d’un véhicule** avec lequel la communication a été perdue.

Je télécharge les logs sur ma VM Kali et j’ouvre le fichier avec [**Wireshark**](https://www.varonis.com/fr/blog/comment-utiliser-wireshark).  
<img src="/images/blog/2.webp" width="800"  />

<br>

### 📡 Analyse des logs TELNET

Après une analyse rapide, je détecte l’endroit où la connexion a été coupée, grâce aux lignes rouges et jaunes. On remarque aussi que la communication utilise le protocole **TELNET**, qui n’est pas sécurisé. Il serait préférable d’utiliser **TLS** ou **SSL** pour garantir la confidentialité des échanges. 👨‍🏫

Je fais un clic droit sur un paquet TCP, puis **Follow > TCP Stream**, ce qui permet de suivre et reconstruire l’ensemble de la communication.  
<img src="/images/blog/3.webp" width="800"  />

On y trouve des identifiants que je mets de côté pour la suite :  
<img src="/images/blog/4.webp" width="800"  />

<br>

### 🔐 Base64 et 1er Flag

Un peu plus bas, deux lignes de commande semblent être encodées en **Base64**. Je les passe dans un [décodeur Base64](https://www.base64encode.org/fr/). La **seconde ligne ne donne rien**, mais la première révèle notre **premier flag** :

> `RM{7aff2a607b13f73cb0936f96e67b210207ae0475}` 😀  
<img src="/images/blog/5.webp" width="800"  />

<br>

### 📲 Analyse UDP et 2e Flag

Ne trouvant rien d’autre, je refais un **Follow** sur les paquets **UDP** au moment où la connexion a été perdue :  
<img src="/images/blog/6.webp" width="800"  />

<br>

Je repère une **adresse IP** et exécute une commande `curl` dessus. Je reçois du HTML avec une redirection vers `/login`.  
<img src="/images/blog/7.webp" width="800"  />

<br>

Je me rends sur cette page depuis mon navigateur :  
<img src="/images/blog/8.webp" width="800"  />

<br>

Je tente les identifiants récupérés précédemment → **échec**. J’essaie ensuite une injection SQL classique avec :

```
' or 1=1 --
```

Coup de chance 🥳, ça fonctionne !

> Pour comprendre les injections SQL, voici une super ressource :  
> [hacksplaining.com](https://www.hacksplaining.com/lessons)

Je suis redirigé vers une page m’invitant à télécharger les **derniers logs du véhicule**.  
<img src="/images/blog/9.webp" width="800"  />

<br>
<br>

### 🧠 Analyse des logs et extraction d’un .zip

Dans ces logs, je trouve :

- Un second **flag** encodé en **hexadécimal**  
- Une **commande suspecte** indiquant qu’un fichier est caché dans le `favicon.ico`  
<img src="/images/blog/10.webp" width="800"  />

<br>

Je vais dans l’onglet Réseau du navigateur et télécharge le `favicon.ico`.  
<img src="/images/blog/11.webp" width="800"  />

<br>

La commande `ls -lh` montre que le fichier pèse **25 Ko**, ce qui est beaucoup. Il doit y avoir un fichier caché.

Grâce à `strings`, je repère :

- `message.txtUT`
- `m4lw3r3UT`

💡 Je soupçonne un fichier `.zip` dissimulé.

Je lance :

```bash
xxd favicon.ico | less
```

Je repère l’en-tête ZIP (504B0304) et la fin (504B0506) :
<img src="/images/blog/12.webp" width="800"  />

<br>

Offsets :

Début : 0x3C20 (15 344 en décimal)

Fin : 0x6230 (25 136 en décimal)

Je lance donc :
```bash
dd if=favicon.ico of=extracted.zip bs=1 skip=15344 count=9792
```
Un fichier .zip est extrait… mais il est protégé par mot de passe 😐

<br>
<br>

### 🔐 Crack du mot de passe et 3e Flag

Dans les logs, je trouve un fichier `.password` contenant une chaîne non-Base64. En le testant comme mot de passe, **le zip s’ouvre**.  
<img src="/images/blog/13.webp" width="800"  />

<br>

Le `.zip` contient :

- Un fichier `message.txt` avec le **3e flag**
- Un fichier nommé **m4lw3r3**

<br>
<br>

### ⚙️ Analyse du binaire C

Je lance `file` sur **m4lw3r3** → c’est un **exécutable compilé en C**.

Après scan antivirus, je l’exécute : il me demande **deux inputs**.

Je lance **GDB** pour comprendre le programme.  
Dans la fonction `main`, je trouve un appel à `strcmp`.

```bash
break *main+165
run
```

<img src="/images/blog/14.webp" width="800"  />

<br>

Je mets un point d’arrêt, exécute, puis observe les registres.
Avec l’analyse de la pile, je trouve le premier input → 3e flag validé ! 🎉

<br>

<img src="/images/blog/15.webp" width="800"  />

Mais il reste un second input…


<br>
<br>

### 🔁 Tentatives en reverse & OpenSSL

Après plusieurs essais infructueux, je décide d’apprendre davantage sur le reverse engineering :

- [Introduction au Reverse Engineering — Partie 1](https://reverse.zip/posts/introduction_au_reverse_partie_1/)  
- [IDA Free (Interactive DisAssembler)](https://hex-rays.com/ida-free)

Malheureusement, un système de **cryptographie avec OpenSSL3** me bloque.  
Je n’ai pas réussi à aller plus loin, malgré de nombreuses heures d’efforts.

Rattrapé par le temps, je n’ai pas pu découvrir les **deux derniers flags**. 😢


<br>
<br>

## 🏁 Conclusion

Ce challenge était très intéressant, malgré ma déception de ne pas avoir trouvé tous les flags.  
Il m’a donné encore plus envie de m’améliorer et d’en apprendre davantage.

J’ai notamment beaucoup progressé en **reverse engineering**, et renforcé ma méthodologie d’analyse.

> ⏱️ Temps total passé : **~30 heures** sur 4 jours  
> (oui, j’ai passé beaucoup de temps sur la crypto 🥶)
