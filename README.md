**Introduction**

Réalisation du CTF COMCYBER X ROOT-ME PRO, 6 flags à trouver du 2 septembre jusqu’au 30 septembre 2024 (4 jours pour moi, je suis arrivé légèrement en retard 😬). Un bon moyen de se tester \! Le challenge prévoit de mettre nos compétences en cybersécurité à l’épreuve à travers une série de défis stimulants dans les domaines du web, forensic, crypto, reverse, stéganographie et réseau. 🌐

**Présentation**

Je suis Sandro Marco, étudiant à l’école 2600\. Je suis passionné par l’informatique depuis toujours et je m’oriente sérieusement vers la cybersécurité depuis 1 an. J’ai pour l’instant tout appris de la cybersécurité en autodidacte et je suis heureux de pouvoir intégrer l’école 2600, qui est une école 100% cyber. PS : Je suis toujours à la recherche d’une alternance :).

**Le Challenge**

Après une rapide inscription au challenge nous arrivons sur une page qui nous explique le contexte et qui nous propose de télécharger les logs réseaux d’un véhicule avec lequel nous avons perdu la communication.

Je télécharge donc les logs sur ma VM kali est ouvre le fichier avec W[ireshark](https://www.varonis.com/fr/blog/comment-utiliser-wireshark)  
<img src="/images/blog/1.webp" width="600" height="450" />

Après une rapide analyse, je détecte rapidement où la connexion a été coupée grâce au ligne rouge et jaune. On remarque aussi que la communication utilise le protocole **TELNET**, qui n’est pas sécurisé. Il serait préférable d’utiliser un protocole comme **TLS** ou **SSL** pour garantir la confidentialité des échanges👨‍🏫. Je décide alors de faire un clic droit sur un paquet TCP, puis de sélectionner **“Follow”** et enfin **“TCP Stream”,** ce qui me permet de suivre et de reconstruire l’ensemble des communications qui se déroulent au sein d’une même connexion TCP. <img src="/images/blog/2.webp" width="600" height="450" />On y trouve des identifiants que je mets de côté pour la suite.:  
<img src="/images/blog/3.webp" width="600" height="450" />
Un peu plus bas, on trouve deux lignes de commande. Elles semblent illisibles à première vue, mais ressemblent à des données encodées en Base64. Je passe donc les deux lignes dans un [décodeur Base64](https://www.base64encode.org/fr/). La seconde ligne ne révèle rien d’intéressant, mais la première me retourne notre premier flag \= RM{7aff2a607b13f73cb0936f96e67b210207ae0475}😀\!!<img src="/images/blog/4.webp" width="600" height="450" />

je ne pense pas trouver autre chose dans ce fichier je décide donc de refaire un follow mais sur le fichier udp la où la connexion a été perdue:

<img src="/images/blog/5.webp" width="600" height="450" />   
Je trouve une adresse IP et exécute une commande curl sur celle-ci pour en tirer des informations. La réponse est du code HTML avec une redirection vers une page /login, ce qui suggère qu'une page web d'authentification s'y trouve.:<img src="/images/blog/6.webp" width="600" height="450" />  
J’entre donc l’adresse dans mon navigateur et tombe sur cette page :  
<img src="/images/blog/7.webp" width="600" height="450" />

J’essaie d’abord le login et mot de passe trouvés plus haut, sans succès. Je tente ensuite une injection SQL avec le mot de passe : **‘ or 1=1 —** , et coup de chance 🥳, ça fonctionne \!

Pour ceux qui ne savent pas ce qu’est une injection SQL, voici un site qui rend l’apprentissage des failles de sécurité accessible et compréhensible : [hacksplaining.com](https://www.hacksplaining.com/lessons).

Je me retrouve alors sur le site avec le véhicule dont la communication a été coupée, et on me propose de télécharger les derniers logs.  
<img src="/images/blog/8.webp" width="600" height="450" />

On y trouve beaucoup d’informations intéressantes, mais aussi de nombreux leurres 🧐.

Voici les deux informations les plus importantes :

* Un second flag en hexadécimal.  
* Au-dessus, la commande suivante, qui suppose qu’un fichier serait caché dans **favicon.** Les favicons sont les icônes qui s’affichent dans les onglets des navigateurs web.

<img src="/images/blog/9.webp" width="600" height="450" />On inspecte la page de login ou du panel et dans la partie réseau on retrouve notre favicon, on télécharge l’image.<img src="/images/blog/10.webp" width="600" height="450" /> Grâce à la commande **ls \-lh**, on remarque que la taille du fichier est de 25k, ce qui est beaucoup pour ce type de fichier (généralement entre 5 et 10k). Cela laisse penser qu’un fichier est caché dedans \!

En utilisant la commande **strings**, on remarque deux chaînes de caractères lisibles : **message.txtUT** et **m4lw3r3UT**. On en déduit donc qu’un fichier .zip est caché dans le favicon.

J’utilise ensuite la commande suivante dans mon terminal pour afficher les données du fichier :

**xxd nom\_du\_fichier | less**

On sait que le format ZIP commence par les caractères **PK** ou **504b** en hexadécimal.  
**<img src="/images/blog/11.webp" width="600" height="450" />** 

😎

**504b 0304** indique le début du fichier, et **504b 0506** en marque la fin.  
Nous avons donc :

* Offset de début : **0x3C20** (où vous avez trouvé **50 4B 03 04**)  
* Offset de fin : **0x6230** (où vous avez trouvé **50 4B 05 06**)

Après un petit calcul, je peux exécuter la commande suivante :

***dd if=favicon.ico of=extracted.zip bs=1 skip=15344 count=9792***

Cette commande me permet de récupérer le **fichier.zip** \! Problème, on me demande un code 😐

En consultant mes notes, je remarque dans le fichier log une ligne de caractères aléatoires qui n’est pas en Base64 et qui est placée dans un fichier .password…

<img src="/images/blog/12.webp" width="600" height="450" />

Et voilà 💁‍♂️.

Je trouve donc un fichier **message.txt** avec le 3ème flag ainsi qu’un fichier nommé **m4lw3r3**. Après avoir exécuté la commande **file** sur le fichier **m4lw3r3**, je comprends qu’il s’agit d’un fichier compilé en C. Je m’assure qu’il ne contient pas de virus, puis je l’exécute : il me demande deux inputs. Je lance GDB (GNU Debugger, un outil de débogage qui permet d’en apprendre plus sur le déroulement d’un programme en C). Je désassemble la fonction **main** pour examiner les fonctions et autres données exécutées par le fichier, et je trouve un appel à **strcmp** (une fonction bien connue en C qui compare deux chaînes de caractères).

<img src="/images/blog/13.webp" width="600" height="450" /> 

J’entre la commande **break \*main+165** (165 étant l’emplacement où se trouve le strcmp), puis j’exécute le programme avec la commande run. (‘test’ est la valeur que j’ai entré pour tester le programme)

<img src="/images/blog/14.webp" width="600" height="450" />

Et voilà le 3ème flag, facile non ? 🤓  
J’ai donc trouvé le premier input, Mais le programme m’en demande un second\! Après de nombreuses tentatives, je me décide à suivre un cours pour consolider mes connaissances en reverse engineering \-\> [**Introduction au Reverse Engineering — Partie 1**](https://reverse.zip/posts/introduction_au_reverse_partie_1/) et bien utiliser l’outil [**IDA** (Interactive DisAssembler)](https://hex-rays.com/ida-free), un outil de désassemblage et d’analyse de logiciels qui permet d’explorer et de comprendre le code binaire d’un programme.  
Malheureusement, après de nombreuses heures, je n’ai pas réussi à aller plus loin. Un système de cryptographie avec OpenSSL3 m’a bloqué, et je n’ai pas pu trouver la solution. Rattrapé par le temps, je n’ai pas pu découvrir les deux derniers flags 😢.

**Conclusion**

Ce challenge était très intéressant, malgré ma déception de ne pas avoir trouvé les derniers flags. Il me donne encore plus envie de m’améliorer et d’en apprendre davantage. J’ai également beaucoup appris en reverse engineering. Le challenge m’a pris environ 4 jours, soit \~30 heures (oui, j’ai passé beaucoup de temps sur la crypto 🥶).