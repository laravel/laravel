<?php

namespace Faker\Provider\cs_CZ;

class Text extends \Faker\Provider\Text
{
    public function realText($maxNbChars = 200, $indexSize = 2)
    {
        $text = parent::realText($maxNbChars, $indexSize);
        $text = str_replace('„', '', $text);

        return str_replace('“', '', $text);
    }

    /**
     * License: PD old 70
     *
     * Title: Krakatit
     * Author: Karel Čapek
     * Release Date: 25. 12. 1923 – 15. 4. 1924
     * Language: Czech
     *
     * @see https://cs.wikisource.org/wiki/Krakatit
     *
     * @var string
     *
     * Karel Čapek
     * KRAKATIT
     * Znění tohoto textu vychází z díla Krakatit tak, jak bylo vydáno v Československém spisovateli v roce 1982
     * (ČAPEK, Karel. Továrna na absolutno ; Krakatit. 12. vyd. Továrny na absolutno, 16. vyd. Krakatitu. Praha :
     * Československý spisovatel, 1982. 476 s. Spisy, sv. 3.).
     * Další díla Karla Čapka naleznete online na www stránkách Městské knihovny v Praze: www.mlp.cz/karelcapek.
     * Elektronické publikování díla Karla Čapka je společným projektem Městské knihovny v Praze,
     * Společnosti bratří Čapků, Památníku Karla Čapka a Českého národního korpusu.
     */
    protected static $baseText = <<<'EOT'
I.
S večerem zhoustla mlha sychravého dne. Je ti, jako by ses protlačoval řídkou
vlhkou hmotou, jež se za tebou neodvratně zavírá. Chtěl bys být doma. Doma, u
své lampy, v krabici čtyř stěn. Nikdy ses necítil tak opuštěn.
Prokop si razí cestu po nábřeží. Mrazí ho a čelo má zvlhlé potem slabosti;
chtěl by si sednout tady na té mokré lavičce, ale bojí se strážníků. Zdá se
mu, že se motá; ano, u Staroměstských mlýnů se mu někdo vyhnul obloukem jako
opilému. Nyní tedy vynakládá veškeru sílu, aby šel rovně. Teď, teď jde proti
němu člověk, má klobouk do očí a vyhrnutý límec. Prokop zatíná zuby, vraští
čelo, napíná všechny svaly, aby bezvadně přešel. Ale zrovna na krok před
chodcem se mu udělá v hlavě tma a celý svět se s ním pojednou zatočí; náhle
vidí zblízka, zblizoučka pár pronikavých očí, jak se do něho vpíchly, naráží
na něčí rameno, vypraví ze sebe cosi jako „promiňte“ a vzdaluje se s
křečovitou důstojností. Po několika krocích se zastaví a ohlédne; ten člověk
stojí a dívá se upřeně za ním. Prokop se sebere a odchází trochu rychleji; ale
nedá mu to, musí se znovu ohlédnout; a vida, ten člověk ještě pořád stojí a
dívá se za ním, dokonce samou pozorností vysunul z límce hlavu jako želva. „Ať
kouká,“ myslí si Prokop znepokojen, „teď už se ani neohlédnu.“ A jde, jak
nejlépe umí; náhle slyší za sebou kroky. Člověk s vyhrnutým límcem jde za ním.
Zdá se, že běží. A Prokop se v nesnesitelné hrůze dal na útěk.
Svět se s ním opět zatočil. Těžce oddychuje, jektaje zuby opřel se o strom a
zavřel oči. Bylo mu strašně špatně, bál se, že padne, že mu praskne srdce a
krev vyšplíchne ústy. Když otevřel oči, viděl těsně před sebou člověka s
vyhrnutým límcem.
„Nejste vy inženýr Prokop?“ ptal se člověk, patrně už poněkolikáté.
„Já… já tam nebyl,“ pokoušel se Prokop cosi zalhávat.
„Kde?“ ptal se muž.
„Tam,“ řekl Prokop a ukazoval hlavou kamsi k Strahovu. „Co na mně chcete?“
„Copak mne neznáš? Já jsem Tomeš. Tomeš z techniky, nevíš už?“
„Tomeš,“ opakoval Prokop, a bylo mu k smrti jedno, jaké to je jméno. „Ano,
Tomeš, to se rozumí. A co – co mi chcete?“
Muž s vyhrnutým límcem uchopil Prokopa pod paží. „Počkej, teď si sedneš,
rozumíš?“
„Ano,“ řekl Prokop a nechal se dovést k lavičce. „Já totiž… mně není dobře,
víte?“ Náhle vyprostil z kapsy ruku zavázanou jakýmsi špinavým cárem.
„Poraněn, víte? Zatracená věc.“
„A hlava tě nebolí?“ řekl člověk.
„Bolí.“
„Tak poslouchej, Prokope,“ řekl člověk. „Teď máš horečku nebo co. Musíš do
špitálu, víš? Je ti zle, to je vidět. Ale aspoň se hleď upamatovat, že se
známe. Já jsem Tomeš. Chodili jsme spolu do chemie. Člověče, rozpomeň se!“
„Já vím, Tomeš,“ ozval se Prokop chabě. „Ten holomek. Co s ním je?“
„Nic,“ řekl Tomeš. „Mluví s tebou. Musíš do postele, rozumíš? Kde bydlíš?“
„Tam,“ namáhal se mluvit Prokop a ukazoval někam hlavou. „U… u Hybšmonky.“
Náhle se pokoušel vstát. „Já tam nechci! Nechoďte tam! Tam je – tam je –“
„Co?“
„Krakatit,“ zašeptal Prokop.
„Co je to?“
„Nic. Neřeknu. Tam nikdo nesmí. Nebo – nebo –“
„Co?“
„Ffft, bum!“ udělal Prokop a hodil rukou do výše.
„Co je to?“
„Krakatoe. Kra-ka-tau. Sopka. Vul-vulkán, víte? Mně to… natrhlo palec. Já
nevím, co…“ Prokop se zarazil a pomalu dodal: „To ti je strašná věc, člověče.“
Tomeš se pozorně díval, jako by něco očekával. „Tak tedy,“ začal po chvilce,
„ty ještě pořád děláš do třaskavin?“
„Pořád.“
„S úspěchem?“
Prokop vydal ze sebe cosi na způsob smíchu. „Chtěl bys vědět, že? Holenku, to
není jen tak. Není – není jen tak,“ opakoval klátě opile hlavou. „Člověče, ono
to samo od sebe samo od sebe –“
„Co?“
„Kra-ka-tit. Krakatit. Krrrakatit. A ono to samo od sebe – Já nechal jen
prášek na stole, víš? Ostatní jsem smetl dododo-do takové piksly. Zu-zůstal
jen poprašek na stole, – a najednou –“
„– to vybuchlo.“
„Vybuchlo. Jen takový nálet, jen prášek, co jsem utrousil. Ani to vidět
nebylo. Tuhle – žárovka – kilometr dál. Ta to nebyla. A já – v lenošce, jako
kus dřeva. Víš, unaven. Příliš práce. A najednou… prásk! Já letěl na zem. Okna
to vyrazilo a – žárovka pryč. Detonace jako – jako když bouchne lydditová
patrona. Stra-strašná brizance. Já – já nejdřív myslel, že praskla ta por-
porcená – ponce – por-ce-lánová, polcelánová, porcenálová, poncelár, jak se to
honem, to bílé, víte, izolátor, jak se to jmenuje? Kře-mi-čitan hlinitý.“
„Porcelán.“
„Piksla. Já myslel, že praskla ta piksla, se vším všudy. Tak rozškrtnu sirku,
a ona tam je celá, ona je celá, ona je celá. A já – jako sloup – až mně sirka
spálila prsty. A pryč – přes pole – potmě – na Břevnov nebo do Střešovic – Aa
někde mě napadlo to slovo. Krakatoe. Krakatit. Kra-ka-tit. Nene, tak to
nenenebylo. Jak to bouchlo, letím na zem a křičím Krakatit. Krakatit. Pak jsem
na to zapomněl. Kdo je tu? Kdo – kdo jste?“
„Kolega Tomeš.“
„Tomeš, aha. Ten všivák! Přednášky si vypůjčoval. Nevrátil mně jeden sešit
chemie. Tomeš, jak se jmenoval?“
„Jiří.“
„Já už vím, Jirka. Ty jsi Jirka, já vím. Jirka Tomeš. Kde máš ten sešit?
Počkej, já ti něco řeknu. Až vyletí to ostatní, je zle. Člověče, to rozmlátí
celou Prahu. Smete. Odfoukne, ft! Až vyletí ta por-ce-lánová dóze, víš?“
„Jaká dóze?“
„Ty jsi Jirka Tomeš, já vím. Jdi do Karlína. Do Karlína nebo do Vysočan, a
dívej se, až to vyletí. Běž, běž honem!“
„Proč?“
„Já toho nadělal cent. Cent Krakatitu. Ne, asi – asi patnáct deka. Tam nahoře,
v té por-ce-lánové dózi. Člověče, až ta vyletí – Ale počkej, to není možné, to
je nesmysl,“ mumlal Prokop chytaje se za hlavu.
„Nu?“
„Proč – proč – proč to nevybuchlo také v té dózi? Když ten prášek – sám od
sebe – Počkej, na stole je zin-zinkový plech – plech – Od čeho to na stole
vybuchlo? Poč-kej, buď tiše, buď tiše,“ drtil Prokop a vrávoravě se zvedl.
„Co je ti?“
„Krakatit,“ zabručel Prokop, udělal celým tělem jakýsi otáčivý pohyb a svalil
se na zem v mrákotách.


II.

První, co si Prokop uvědomil, bylo, že se s ním všechno otřásá v drnčivém
rachotu a že ho někdo pevně drží kolem pasu. Hrozně se bál otevřít oči;
myslel, že se to na něj řítí. Ale když to neustávalo, otevřel oči a viděl před
sebou matný čtyřúhelník, kterým se sunou mlhavé světelné koule a pruhy. Neuměl
si to vysvětlit; díval se zmateně na uplývající a poskakující mátohy, trpně
odevzdán ve vše, co se s ním bude dít. Pak pochopil, že ten horlivý rachot
jsou kola vozu a venku že míjejí jenom svítilny v mlze; a unaven tolikerým
pozorováním zavřel opět oči a nechal se unášet.
„Teď si lehneš,“ řekl tiše hlas nad jeho hlavou; „spolkneš aspirin a bude ti
líp. Ráno ti přivedu doktora, ano?“
„Kdo je to,“ ptal se Prokop ospale.
„Tomeš. Lehneš si u mne, Prokope. Máš horečku. Kde tě co bolí?“
„Všude. Hlava se mi točí. Tak, víš –“
„Jen tiše lež. Uvařím ti čaj a vyspíš se. Máš to z rozčilení, víš. To je
taková nervová horečka. Do rána to přejde.“
Prokop svraštil čelo v námaze vzpomínání. „Já vím,“ řekl po chvíli
starostlivě. „Poslyš, ale někdo by měl tu pikslu hodit do vody. Aby
nevybuchla.“
„Bez starosti. Teď nemluv.“
„A… já bych snad mohl sedět. Nejsem ti těžký?“
„Ne, jen lež.“
„– – A ty máš ten můj sešit chemie,“ vzpomněl si Prokop najednou.
„Ano, dostaneš jej. Ale teď klid, slyšíš?“
„Já ti mám tak těžkou hlavu –“
Zatím drkotala drožka nahoru Ječnou ulicí. Tomeš slabounce hvízdal nějakou
melodii a díval se oknem. Prokop sípavě dýchal s tichým sténáním. Mlha smáčela
chodníky a vnikala až pod kabát svým sychravým slizem; bylo pusto a pozdě.
„Už tam budeme,“ řekl Tomeš nahlas. Drožka se čerstvěji rozhrčela na náměstí a
zahnula vpravo. „Počkej, Prokope, můžeš udělat pár kroků? Já ti pomohu.“
S námahou vlekl Tomeš svého hosta do druhého patra, Prokop si připadal jaksi
lehký a bez váhy, a nechal se skoro vynést po schodech nahoru; ale Tomeš silně
oddechoval a utíral si pot.
„Viď, jsem jako nitě,“ divil se Prokop.
„Nu ovšem,“ mručel udýchaný Tomeš odemykaje svůj byt.
Prokopovi bylo jako malému dítěti, když jej Tomeš svlékal. „Má maminka,“ začal
něco povídat, „když má maminka, to už je, to už je dávno, tatínek seděl u
stolu, a maminka mne nosila do postele, rozumíš?“
Pak už byl v posteli, přikryt po bradu, jektal zuby a díval se, jak se Tomeš
točí u kamen a rychle zatápí. Bylo mu do pláče dojetím, lítostí a slabostí, a
pořád brebentil; uklidnil se, až dostal na čelo studený obkladek. Tu se tiše
díval po pokoji; bylo tu cítit tabák a ženu.
„Ty jsi kujón, Tomši,“ ozval se vážně. „Pořád máš holky.“
Tomeš se k němu obrátil. „Nu, a co?“
„Nic. Co vlastně děláš?“
Tomeš mávl rukou. „Mizerně, kamaráde. Peníze nejsou.“
„Flámuješ.“
Tomeš jen potřásl hlavou.
„A je tě škoda, víš?“ začal Prokop starostlivě. „Ty bys mohl – Koukej, já
dělám už dvanáct let.“
„A co z toho máš?“ namítl Tomeš příkře.
„No, sem tam něco. Prodal jsem letos třaskavý dextrin.“
„Zač?“
„Za deset tisíc. Víš, nic to není, hloupost. Taková pitomá bouchačka, pro
doly. Ale kdybych chtěl –“
„Je ti už líp?“
„Krásně mi je. Já jsem ti našel metody! Člověče, jeden nitrát ceru, to ti je
vášnivá potvora; a chlor, chlor, tetrastupeň chlordusíku se zapálí světlem.
Rozsvítíš žárovku, a prásk! Ale to nic není. Koukej,“ prohlásil náhle
vystrkuje zpod pokrývky hubenou, děsně zkomolenou ruku. „Když něco vezmu do
ruky, tak… v tom cítím šumět atomy. Zrovna to mravenčí. Každá hmota mravenčí
jinak, rozumíš?“
„Ne.“
„To je síla, víš? Síla v hmotě. Hmota je strašně silná. Já… já hmatám, jak se
to v ní hemží. Drží to dohromady… s hroznou námahou. Jak to uvnitř rozvikláš,
rozpadne se, bum! Všechno je exploze. Když se rozevře květina, je to exploze.
Každá myšlenka, to je takové prasknutí v mozku. Když mně podáš ruku, cítím,
jak v tobě něco exploduje. Já mám takový hmat, člověče. A sluch. Všechno šumí,
jako šumivý prášek. To jsou samé malinkaté výbuchy. Mně ti tak hučí v hlavě…
Ratatata, jako strojní puška.“
„Tak,“ řekl Tomeš, „a teď spolkni tuhleten aspirin.“
„Ano. Třa-třaskavý aspirin. Perchlorovaný acetylsalicylazid. To nic není.
Člověče, já jsem našel exotermické třaskaviny. Každá látka je vlastně
třaskavina. Voda… voda je třaskavina. Hlína… a vzduch jsou třaskaviny. Peří,
peří v peřině je taky třaskavina. Víš, zatím to má jen teoretický význam. A já
jsem našel atomové výbuchy. Já – já – já jsem udělal alfaexploze. Roz-pad-ne
se to na plus plus částice. Žádná termochemie. De-struk-ce. Destruktivní
chemie, člověče. To ti je ohromná věc, Tomši, čistě vědecky. Já ti mám doma
tabulky… Lidi, kdybych já měl aparáty! Ale já mám jen oči… a ruce… Počkej, až
to napíšu!“
„Nechce se ti spát?“
„Chce. Jsem – dnes – unaven. A co tys pořád dělal?“
„Nu, nic. Život.“
„Život je třaskavina, víš? Prásk, člověk se narodí a rozpadne se, bum! A nám
se zdá, že to trvá bůhvíkolik let, viď? Počkej, já jsem teď něco spletl, že?“
„Docela v pořádku, Prokope. Možná že zítra udělám bum. Nebudu-li mít totiž
peníze. Ale to je jedno, starouši, jen spi.“
„Já bych ti půjčil, nechceš?“
„Nech. Na to bys nestačil. Snad ještě můj tatík –“ Tomeš mávl rukou.
„Tak vidíš, ty máš ještě tatínka,“ ozval se Prokop po chvíli s náhlou
měkkostí.
„Nu ano. Doktor v Týnici.“ Tomeš vstal a přecházel po pokoji. „Je to mizérie,
člověče, mizérie. Mám to nahnuté, nu! A nestarej se o mne. Já už – něco
udělám. Spi!“
Prokop se utišil. Polozavřenýma očima viděl, jak Tomeš sedá ke stolku a hrabe
se v nějakých papírech. Bylo mu jaksi sladko naslouchat šustění papíru a
tichému hukotu ohně v kamnech. Člověk skloněný u stolku opřel hlavu o dlaně a
snad ani nedýchal; a Prokopovi bylo, že leží doma a vidí svého staršího
bratra, svého bratra Josefa; učí se z knížek elektrotechnice a bude zítra
dělat zkoušku; a Prokop usnul horečným spánkem.


III.

Zdálo se mu, že slyší hukot jakoby nesčetných kol. „To je nějaká továrna,“
myslel si a běžel po schodech nahoru. Zničehonic se ocitl před velikými
dveřmi, kde stálo na skleněné tabulce: Plinius. Zaradoval se nesmírně a vešel
dovnitř. „Je tu pan Plinius?“ ptal se nějaké slečinky u psacího stroje. „Hned
přijde,“ řekla slečinka, a tu k němu přistoupil vysoký oholený muž v cutawayi
a s ohromnými kruhovými skly na očích. „Co si přejete?“ řekl.
Prokop se zvědavě díval do jeho neobyčejně výrazné tváře. Mělo to britskou
hubu a vypouklé rozježděné čelo, na skráni bradavici zvící šestáku a bradu
jako filmový herec. „Vy – vy – račte být – Plinius?“
„Prosím,“ řekl vysoký muž a krátkým gestem mu ukázal do své pracovny.
„Jsem velmi… je mi… ohromnou ctí,“ koktal Prokop usedaje.
„Co si přejete?“ přerušil ho vysoký muž.
„Já jsem rozbil hmotu,“ prohlásil Prokop. Plinius nic; hrál si jen s ocelovým
klíčkem a zavíral těžká víčka pod skly.
„To je totiž tak,“ začal Prokop překotně. „V-v-všecko se rozpadá, že? Hmota je
křehká. Ale já udělám, že se to rozpadne najednou, bum! Výbuch, rozumíte? Na
padrť. Na molekuly. Na atomy. Ale já jsem rozbil také atomy.“
„Škoda,“ řekl Plinius povážlivě.
„Proč – jaká škoda?“
„Škoda něco rozbít. I atomu je škoda. Nu tak dál.“
„Já… rozbiju atom. Já vím, že už Rutherford… Ale to byla jen taková páračka se
zářením, víte? To nic není. To se musí en masse. Jestli chcete, já vám
rozbourám tunu bismutu; rozštípne to ce-celý svět, ale to je jedno. Chcete?“
„Proč byste to dělal?“
„Je to… vědecky zajímavé,“ zmátl se Prokop. „Počkejte, jakpak bych vám to… To
je – to je vám ne-smír-ně zajímavé.“ Chytil se za hlavu. „Počkejte, mně
praskne hla-va; to bude – vědecky – ohromně zajímavé, že? Aha, aha,“ vyhrkl s
úlevou, „já vám to vyložím. Dynamit – dynamit trhá hmotu na kusy, na balvany,
ale benzoltrioxozonid ji roztrhá na prášek; udělá jen malou díru, ale
rrrozdrtí hmotu nana-na submikroskopickou padrť, rozumíte? To dělá detonační
rychlost. Hmota nemá čas ustoupit; nemůže se už ani roz-rozhrnout, roztrhnout,
víte? A já… jjjá jsem stupňoval detonační rychlost. Argonozonid.
Chlorargonoxozonid. Tetrargon. A pořád dál. Pak už ani vzduch nemůže ustoupit;
je stejně tuhý jako… jako ocelová deska. Roztrhá se na molekuly. A pořád dál.
A najednou vám… od jisté rychlosti… začne brizance děsně stoupat. Roste…
kvadraticky. Já koukám jako blázen. Odkud se to bere? Kde kde kde se najednou
vzala ta energie?“ naléhal Prokop zimničně. „Tak řekněte.“
„Nu, třeba v atomu,“ mínil Plinius.
„Aha,“ prohlásil Prokop vítězně a utřel si pot. „Tady je ten vtip. Jednoduše v
atomu. Ono to… vrazí atomy do sebe… a… sss… serve betaplášť… a jádro se musí
rozpadnout. To je alfaexploze. Víte, kdo jsem? Já jsem první člověk, který
překročil koeficient stlačitelnosti, pane. Já jsem našel atomové výbuchy. Já…
já jsem vyrazil z bismutu tantal. Poslyšte, víte vy, kolik je vy-výkonu v
jednom gramu rtuti? Čtyři sta dvaašedesát miliónů kilogramometrů. Hmota je
děsně silná. Hmota je regiment, který přešlapuje na místě: ráz dva, ráz dva;
ale dejte ten pravý povel, a regiment vyrazí v útok, en evant! To je výbuch,
rozumíte? Hurá!“
Prokop se zarazil vlastním křikem; v hlavě mu bušilo tak, že přestal cokoli
vnímat. „Promiňte,“ řekl, aby zamluvil rozpaky, a hledal třesoucí se rukou své
pouzdro na cigára. „Kouříte?“
„Ne.“
„Již staří Římané kouřili,“ ujišťoval Prokop a otevřel pouzdro; byly tam samé
těžké patrony. „Zapalte si,“ nutil, „to je lehoučký Nobel Extra.“ Sám ukousl
špičku tetrylové patrony a hledal sirky. „To nic není,“ začal, „ale znáte
třaskavé sklo? Škoda. Poslyšte, já vám mohu udělat výbušný papír. Napíšete
psaní, někdo to hodí do ohně a prásk! celý barák se sesype. Chcete?“
„K čemu?“ ptal se Plinius zvedaje obočí.
„Jen tak. Síla musí ven. Já vám něco povím. Kdybyste chodil po stropě, tak co
vám z toho vznikne? Já především kašlu na valenční teorie. Všecko se dá dělat.
Slyšíte, jak to venku rachotí? To slyšíte růst trávu: samé výbuchy. Každé
semínko je třaskavá kapsle, která vyletí. Puf, jako raketa. A ti hlupáci si
myslí, že není žádná tautomerie. Já jim ukážu takovou merotropii, že budou z
toho blázni. Samá laboratorní zkušenost, pane.“
Prokop cítil s hrůzou, že žvaní nesmysly; chtěl tomu uniknout a mlel tím
rychleji, pleta páté přes deváté. Plinius vážně kýval hlavou; dokonce komihal
celým tělem hlouběji a pořád hlouběji, jako by se klaněl. Prokop drmolil
zmatené formule a nemohl se zastavit, poule oči na Plinia, který se komihal s
rostoucí rychlostí jako stroj. Podlaha pod ním se začala houpat a zvedat.
„Ale tak přestaňte, člověče,“ zařval Prokop zděšen a probudil se. Místo Plinia
viděl Tomše, který neobraceje se od stolku bručel: „Nekřič, prosím tě.“
„Já nekřičím,“ řekl Prokop a zavřel oči. V hlavě mu hučelo rychlými a
bolestnými tepy.
Zdálo se mu, že letí přinejmenším rychlostí světla; nějak se mu svíralo srdce,
ale to dělá jen Fitzgerald-Lorentzovo zploštění, řekl si; musím být placatý
jako lívanec. A najednou se proti němu vyježí nesmírné skleněné hranoly; ne,
jsou to jenom nekonečné hladce vybroušené roviny, jež se protínají a
prostupují v břitkých úhlech jako krystalografické modely; a proti jedné
takové hraně je hnán úžasnou rychlostí. „Pozor,“ zařval sám na sebe, neboť v
tisícině vteřiny se musí roztříštit; ale tu již bleskově odletěl zpět a rovnou
proti hrotu obrovského jehlanu; odrazil se jako paprsek a byl vržen na
skleněně hladkou stěnu, smeká se podle ní, sviští do ostrého úhlu, kmitá
šíleně mezi jeho stěnami, je hozen pozpátku nevěda proti čemu, zas odmrštěn
dopadá bradou na ostrou hranu, ale v poslední chvíli ho to odhodí vzhůru; nyní
si roztřískne hlavu o euklidovskou rovinu nekonečna, ale již se řítí střemhlav
dolů, dolů do tmy; prudký náraz, bolestné cuknutí v celém těle, ale hned zas
se zvedl a dal se na útěk. Uhání labyrintickou chodbou a za sebou slyší dupot
pronásledovatelů; chodba se úží, svírá se, její stěny se přirážejí k sobě
děsným a neodvratným pohybem; i dělá se tenký jako šídlo, zatajuje dech a
upaluje v bláznivé hrůze, aby tudy proběhl, než ho ty stěny rozdrtí. Zavřelo
se to za ním s kamenným nárazem, zatímco sám svistí do propasti podle ledově
čišící zdi. Strašný úder, a ztrácí vědomí; když procitl, vidí, že je v černé
tmě; hmatá po slizkých kamenných stěnách a křičí o pomoc, ale z jeho úst
nevychází zvuku; taková je tu tma. Jektaje hrůzou klopýtá po dně propasti;
nahmatá postranní chodbu, i vrhá se do ní; jsou to vlastně. schody, a nahoře,
nekonečně daleko svítá malinký otvor jako v šachtě; běží tedy nahoru po
nesčíslných a strašně příkrých stupních; ale nahoře není než plošinka,
lehoučká plechová platforma drnčící a chvějící se nad závratnou hlubinou, a
dolů se šroubem točí jen nekonečné schůdky ze železných plátů. A tu již za
sebou slyšel supění pronásledovatelů. Bez sebe hrůzou se řítil a točil po
schůdkách dolů, a za ním železně řinčí a rachotí dupající zástup nepřátel. A
najednou vinuté schody se končí ostře v prázdnu. Prokop zavyl, rozpřáhl ruce a
pořád ještě víře padal do bezdna. Hlava se mu zatočila, neviděl už a neslyšel;
váznoucíma nohama běžel nevěda kam, drcen strašným a slepým puzením, že musí
kamsi dorazit, než bude pozdě. Rychleji a rychleji ubíhal nekonečným
koridorem; čas od času míjel semafor, na kterém pokaždé vyskočila vyšší
číslice: 17, 18, 19. Najednou pochopil, že běhá v kruhu a ta čísla že udávají
počet jeho oběhů. 40, 41. Popadla ho nesnesitelná hrůza, že přijde pozdě a že
se odtud nedostane; svištěl zběsilou rychlostí, takže se semafor jenom mihal
jako telegrafní tyče z rychlíku; a ještě rychleji! nyní už semafor ani
neubíhá, nýbrž stojí na jednom místě a odpočítává bleskovou rychlostí tisíce a
desettisíce oběhů, a nikde není východ z té chodby, a chodba je na pohled
rovná a lesklá jako hamburský tunel, a přece se vrací kruhem; Prokop vzlyká
děsem: to je Einsteinův vesmír, a já musím dojít, než bude pozdě! Náhle zazněl
strašný výkřik, a Prokop ustrnul: je to hlas tatínkův, někdo ho vraždí; i jal
se obíhat ještě rychleji, semafor zmizel, udělala se tma; Prokop tápal po
stěnách a nahmatal zamčené dveře, a za nimi je slyšet to zoufalé bědování a
rány pokáceného nábytku. Řva hrůzou zarývá Prokop nehty do dveří, štípe je a
rozškrabává; vytrhal je po třískách a našel za nimi staré známé schody, jež ho
denně vedly domů, když byl maličký; a nahoře dusí se tatínek, někdo ho škrtí a
smýká jím po zemi. Křiče vyletí Prokop nahoru, je doma na chodbě, vidí konve a
chlebovou skříň maminčinu a pootevřené dveře do kuchyně, a tam uvnitř chroptí
a prosí tatínek, aby ho nezabíjeli; někdo mu tluče hlavou o zem; chce mu jít
na pomoc, ale nějaká slepá, bláznivá moc ho nutí, aby tady na chodbě běhal
dokola, pořád rychleji dokolečka a chechtal se jíkavě, zatímco uvnitř skomírá
a dusí se tatínkovo sténání. A neschopen vykročit ze závratného bludného
kruhu, řítě se stále rychleji ryčel Prokop šíleným smíchem hrůzy.
Tu se probudil zalit potem a jektaje zuby. Tomeš mu stál u hlav a dával mu na
rozžhavené čelo nový chladivý obklad.
„To je dobře, to je dobře,“ mumlal Prokop, „já už nebudu spát.“ I ležel tiše a
díval se na Tomše, jak sedí u lampy. Jirka Tomeš, říkal si, a počkejme, pak
kolega Duras, a Honza Buchta, Sudík, Sudík, Sudík, a kdo ještě? Sudík, Trlica,
Trlica, Pešek, Jovanovič, Mádr, Holoubek, co nosil brejle, to je náš ročník na
chemii. Bože, a který je tamhleten? Aha, to je Vedral, ten padl v roce
šestnáct, a za ním sedí Holoubek, Pacovský, Trlica, Šeba, celý ročník. A tu
slyšel najednou: „Pan Prokop bude kolokvovat.“
Lekl se nesmírně. U katedry sedí profesor Wald a tahá se suchou ručičkou za
vousy, jako vždy. „Povězte,“ praví profesor Wald, „co víte o třaskavinách.“
„Třaskaviny třaskaviny,“ začíná Prokop nervózně, „jejich výbušnost záleží na
tom, že že že se náhle vyvine veliký objem plynu, který který se vyvine z
mnohem menšího objemu výbušné masy… Prosím, to není správné.“
„Jak to?“ táže se Wald přísně.
„Já já já jsem našel alfavýbuchy. Výbuch totiž nastane rozpadem atomu.
Částečky atomu se rozletí – rozletí –“
„Nesmysl,“ přeruší ho profesor. „Není žádných atomů.“
„Jsou jsou jsou,“ drtil Prokop. „Prosím, já já já to dokážu –“
„Překonaná teorie,“ bručí profesor. „Nejsou vůbec žádné atomy, jsou jenom
gumetály. Víte, co je to gumetál?“
Prokop se zapotil úlekem. Toho slova nikdy v životě neslyšel. Gumetál? „To
neznám,“ vydechl stísněně.
„Tak vidíte,“ řekl suše Wald. „A pak si troufáte dělat kolokvium. Co víte o
Krakatitu?“
Prokop se nesmírně zarazil. „Krakatit,“ šeptal, „to je… to je úplně nová
třaskavina, která… která dosud…“
„Čím se zanítí? Čím? Čím exploduje?“
„Hertzovými vlnami,“ vyhrkl Prokop s úlevou.
„Jak to víte?“
„Protože mně zničehonic vybuchla. Protože… protože nebyl žádný jiný impuls. A
protože –“
„Nu?“
„… její syn-syntéza… se mně povedla za-za-za… vysokofrekvenční oscilace. Není
to dosud vyvyvysvětleno; ale já myslím, že – – že to byly nějaké
elektromagnetické vlny.“
„Byly. Já to vím. Teď napište na tabuli chemicky vzorec Krakatitu.“
Prokop vzal kus křídy a načmáral na tabuli svůj vzorec.
„Přečtěte.“
Prokop odříkal vzorec nahlas. Tu vstal profesor Wald a řekl najednou jakýmsi
docela jiným hlasem: „Jak? Jak je to?“
Prokop opakoval formuli.
„Tetrargon?“ ptal se profesor rychle. „Pb kolik?“
„Dvě.“
„Jak se to dělá?“ tázal se hlas podivně blízce. „Postup! Jak se to dělá? Jak?…
Jak se dělá Krakatit?“
Prokop otevřel oči. Nad ním se skláněl Tomeš s tužkou a zápisníkem v ruce a
bez dechu se díval na jeho rty.
„Co?“ mumlal Prokop neklidně. „Co chceš? Jak… jak se to dělá?“
„Něco se ti zdálo,“ řekl Tomeš a schoval zápisník za zády. „Spi, člověče,
spi.“


IV.

Teď jsem něco vyžvanil, uvědomoval si Prokop jasnějším cípem mozku; ale jinak
mu to bylo svrchovaně lhostejno; chtělo se mu jen spát, nesmírně spát. Viděl
jakýsi turecký koberec, jehož vzor se bez konce přesunoval, prostupoval a
měnil. Nebylo to nic, a přece ho to jaksi rozčilovalo; i ve spaní zatoužil
vidět znovu Plinia. Snažil se vybavit si jeho podobu; místo toho měl před
sebou ohavnou zešklebenou tvář, jež skřípala žlutými vyžranými zuby, až se
drtily, a pak je po kouskách vyplivovala. Chtěl tomu uniknout; napadlo ho
slovo „rybář“, a hle, zjevil se mu rybář nad šedivou vodou i s rybami v
čeřenu; řekl si „lešení“, a viděl skutečné lešení do poslední skoby a vazby.
Dlouho se bavil tím, že vymýšlel slova a pozoroval obrázky jimi promítnuté;
ale pak, pak už si živou mocí nemohl na žádné slovo vzpomenout. Namáhal se
usilovně, aby našel aspoň jedno jediné slovo nebo věc, ale marně; tu ho zalila
hrůza bezmoci studeným potem. Musím postupovat metodicky, umínil si; začnu zas
od začátku, nebo jsem ztracen. Šťastně si vzpomněl na slovo „rybář“, ale
zjevil se mu hliněný prázdný galon od petroleje; bylo to děsné. Řekl si
„židle“, a ukázal se mu s podivnou podrobností dehtovaný tovární plot s
trochou smutné zaprášené trávy a rezavými obručemi. To je šílenství, řekl si s
mrazivou jasností; to je, pánové, typická pomatenost, hyperofabula ugongi
dugongi Darwin. Tu se mu tento odborný název zazdál neznámo proč ukrutně
směšný, a dal se do hlasitého, zrovna zalykavého smíchu, jímž se probudil.
Byl úplně zpocen a odkopán. Díval se horečnýma očima na Tomše, který chvatně
přecházel po pokoji a házel nějaké věci do kufříku; ale nepoznával ho.
„Poslyšte, poslyšte,“ začal, „to je k smíchu, poslyšte, tak počkejte, to
musíte, poslyšte –“ Chtěl říci jako vtip ten podivuhodný odborný název, a sám
se smál předem; ale živou mocí si nemohl vzpomenout, jak to vlastně bylo, i
rozmrzel se a umkl.
Tomeš si oblékl ulstr a narazil čepici; a když už bral kufřík, zaváhal a sedl
si na pelest k Prokopovi. „Poslyš, starouši,“ řekl starostlivě, „já teď musím
odejet. K tátovi, do Týnice. Nedá-li mně peníze, tak – se už nevrátím, víš?
Ale nic si z toho nedělej. Ráno sem přijde domovnice a přivede ti doktora,
ano?“
„Kolik je hodin?“ ptal se Prokop netečně.
„Čtyři… Čtyři a pět minut. Snad… ti tu nic neschází?“
Prokop zavřel oči, odhodlán nezajímat se už o nic na světě. Tomeš ho pečlivě
přikryl, a bylo ticho.
Náhle otevřel oči dokořán. Viděl nad sebou neznámý strop a po jeho kraji běží
neznámý ornament. Sáhl rukou po svém nočním stolku, a hmátl do prázdna.
Obrátil se polekán, a místo svého širokého laboratorního pultu vidí nějaký
cizí stolek s lampičkou. Tam, kde bývalo okno, je skříň; kde stávalo umyvadlo,
jsou jakési dveře. Zmátl se tím vším nesmírně; nedovedl pochopit, co se s ním
děje, kde se octl, a přemáhaje závrať usedl na posteli. Pomalu si uvědomil, že
není doma, ale nemohl si vzpomenout, jak se sem dostal. „Kdo je to,“ zeptal se
hlasitě nazdařbůh, stěží hýbaje jazykem. „Pít,“ ozval se po chvíli, „pít!“
Bylo trýznivé ticho. Vstal z postele a trochu vrávoravě šel hledat vodu. Na
umyvadle našel karafu a pil z ní dychtivě; a když se vracel do postele,
podlomily se mu nohy a usedl na židli, nemoha dále. Seděl snad hodně dlouho;
pak ho roztřásla zima, neboť se celý polil vodou z karafy, a přišlo mu líto
sebe sama, že je kdesi a neví sám kde, že ani do postele nedojde a že je tak
bezradně a bezmocně sám; tu propukl v dětský vzlykavý pláč.
Když se trochu vyplakal, bylo mu v hlavě jasněji. Dokonce mohl dojít až k
posteli a ulehl jektaje zuby; a sotva se zahřál, usnul mrákotným spánkem beze
snu.
Když se probudil, byla roleta vytažena do šedivého dne a v pokoji trochu
pouklizeno; nedovedl pochopit, kdo to udělal, ale jinak se pamatoval na vše,
na včerejší explozi, na Tomše i na jeho odjezd. Zato ho třeštivě bolela hlava,
bylo mu těžko na prsou a drásavě ho mučil kašel. Je to špatné, říkal si, je to
docela špatné; měl bych jít domů a lehnout si. Vstal tedy a začal se pomalu
strojit chvílemi odpočívaje. Bylo mu, jako by mu něco drtilo hrozným tlakem
prsa. Usedl pak netečný ke všemu a těžce dýchal.
Tu krátce, jemně zazněl zvonek. Vzchopil se s námahou a šel otevřít. Na prahu
v chodbě stála mladá dívka s tváří zastřenou závojem.
„Bydlí tady… pan Tomeš?“ ptala se spěšně a stísněně.
„Prosím,“ řekl Prokop a ustoupil jí z cesty; a když, trochu váhajíc, těsně
podle něho vcházela dovnitř, zavála na něj slabounká a spanilá vůně, že
rozkoší vzdychl.
Posadil ji vedle okna a usedl proti ní, drže se zpříma, jak nejlépe dovedl.
Cítil, že samým úsilím vypadá přísně a strnule, což uvádělo do nesmírných
rozpaků jeho i dívku. Hryzala si pod závojem rty a klopila oči; ach, líbezná
hladkost tváře, ach, ruce malé a hrozně rozčilené! Náhle zvedla oči, a Prokop
zatajil dech omámen úžasem; tak krásná se mu zdála.
„Pan Tomeš není doma?“ ptala se dívka.
„Tomeš odejel,“ řekl Prokop váhavě. „Dnes v noci, slečno.“
„Kam?“
„Do Týnice, k svému otci.“
„A vrátí se?“
Prokop pokrčil rameny.
Dívka sklopila hlavu a její ruce s něčím zápasily. „A řekl vám, proč – proč –“
„Řekl.“
„A myslíte, že – že to udělá?“
„Co, slečno?“
„Že se zastřelí.“
Prokop si bleskem vzpomněl, že viděl Tomše ukládat revolver do kufříku. ,Možná
že zítra udělám bum,‘ slyšel jej znovu drtit mezi zuby. Nechtěl nic říci, ale
vypadal asi velmi povážlivě.
„Ó bože, ó bože,“ vypravila ze sebe dívka, „ale to je strašné! Řekněte,
řekněte –“
„Co, slečno?“
„Kdyby – kdyby někdo mohl za ním jet! Kdyby mu někdo řekl – kdyby mu dal –
Vždyť by to nemusel udělat, chápete? Kdyby někdo za ním ještě dnes jel –“
Prokop se díval na její zoufalé ruce, jež se zatínaly a spínaly.
„Já tam tedy pojedu, slečno,“ řekl tiše. „Náhodou… mám snad v tu stranu
nějakou cestu. Kdybyste chtěla – já –“
Dívka zvedla hlavu. „Skutečně,“ vyhrkla radostně, „vy byste mohl –?“
„Já jsem jeho… starý kamarád, víte?“ vysvětloval Prokop. „Chcete-li mu něco
vzkázat… nebo poslat… já ochotně…“
„Bože, vy jste hodný,“ vydechla dívka.
Prokop se slabě začervenal. „To je maličkost, slečno,“ bránil se. „Náhodou…
mám zrovna volný čas… stejně chci někam jet, a vůbec –“ Mávl v rozpacích
rukou. „To nestojí za řeč. Udělám všecko, co chcete.“
Dívka se zarděla a honem se dívala jinam. „Ani nevím, jak bych… vám měla
děkovat,“ řekla zmateně. „Mně je tak líto, že… že vy… Ale je to tak důležité –
A pak, vy jste jeho přítel – Nemyslete si, že já sama –“ Tu se přemohla a
upřela na Prokopa čiré oči. „Já mu něco musím poslat. Od někoho jiného. Já vám
nemohu říci –“
„Není třeba,“ řekl Prokop rychle. „Já mu to dám, a je to. Já jsem tak rád, že
mohu vám… že mohu jemu… Prší snad?“ ptal se náhle dívaje se na její zrosenou
kožišinku.
„Prší.“
„To je dobře,“ mínil Prokop; myslel totiž na to, jak příjemně by chladilo,
kdyby na tu kožišinku směl položit čelo.
„Já to tu nemám,“ řekla vstávajíc. „Bude to jen malý balíček. Kdybyste mohl
počkat… Já vám to přinesu za dvě hodiny.“
Prokop se velmi strnule uklonil; bál se totiž, že ztratí rovnováhu. Ve dveřích
se obrátila a pohlédla na něj upřenýma očima. „Na shledanou.“ A byla ta tam.
Prokop usedl a zavřel oči. Krupičky deště na kožišince, hustý a orosený závoj;
zastřený hlas, vůně, neklidné ruce v těsných, maličkých rukavičkách; chladná
vůně, pohled jasný a matoucí pod sličným, pevným obočím. Ruce na klíně, měkké
řasení sukně na silných kolenou, ach, maličké ruce v těsných rukavicích! Vůně,
temný a chvějící se hlas, líčko hladké a pobledlé. Prokop zatínal zuby do
chvějících se rtů. Smutná, zmatená a statečná. Modrošedé oči, oči čisté a
světelné. Ó bože, ó bože, jak se tiskl závoj k jejím rtům!
Prokop zasténal a otevřel oči. Je to Tomšova holka, řekl si se slepým vztekem.
Věděla kudy jít, není tu poprvé. Snad tady… zrovna tady v tom pokoji – –
Prokop si v nesnesitelné trýzni vrýval nehty do dlaní. A já hlupák se nabízím,
že pojedu za ním! Já hlupák, já mu ponesu psaníčko! Co – co – co mi je vůbec
po ní?
Tu ho napadla spásná myšlenka. Uteku domů, do svého laboratorního baráku tam
nahoře. A ona, ať si sem přijde! ať si pak dělá, co chce! Ať – ať – ať si jede
za ním sama, když… když jí na tom záleží –
Rozhlédl se po pokoji; viděl zválenou postel, zastyděl se a ustlal ji, jak byl
zvyklý doma. Pak se mu nezdála dost slušně ustlaná, přestlal ji, rovnal a
hladil, a pak už rovnal všechno všudy, uklízel, pokoušel se pěkně zřasit i
záclony, načež usedl s hlavou zmotanou a hrudí drcenou bolestným tlakem a
čekal.


V.

Zdálo se mu, že jde ohromnou zelinářskou zahradou; kolem dokola nic než samé
zelné hlávky, ale nejsou to hlávky, nýbrž zešklebené a olezlé, krhavé a
blekotající, nestvůrné, vodnaté, trudovité a vyboulené hlavy lidské; vyrůstají
z hubených košťálů a lezou po nich odporné zelené housenky. A hle, přes pole k
němu běží dívka se závojem na tváři; zvedá trochu sukni a přeskakuje lidské
hlávky. Tu vyrůstají zpod každé z nich nahé, úžasně tenké a chlupaté ruce a
sahají jí po nohou a po sukních. Dívka křičí v šílené hrůze a zvedá sukni
výše, až nad silná kolena, obnažuje bílé nohy a snaží se přeskočit ty
chňapající ruce. Prokop zavírá oči; nesnese pohled na její bílé silné nohy, a
šílí úzkostí, že ji ty zelné hlávky zhanobí. Tu vrhá se na zem a uřezává
kapesním nožem první hlávku; ta zvířecky ječí a cvaká mu vyžranými zuby po
rukou. Nyní druhá, třetí hlávka; Kriste Ježíši, kdy skosí to ohromné pole, než
se dostane k dívce zápasící tam na druhé straně nekonečné zahrady? Zběsile
vyskakuje a šlape po těch příšerných hlavách, rozdupává je, kope do nich;
zaplete se nohama do jejich tenkých, přísavných pracek, padá, je uchopen,
rván, dušen, a vše mizí.
Vše mizí v závratném víření. A náhle se ozve zblízka zastřený hlas: „Nesu vám
ten balíček.“ Tu vyskočil a otevřel oči, a před ním stojí děvečka z Hybšmonky,
šilhavá a těhotná, se zmáčeným břichem, a podává mu cosi zabaleného v mokrém
hadru. To není ona, trne bolestně Prokop, a rázem vidí vytáhlou smutnou
prodavačku, která mu dřevěnými tyčinkami roztahuje rukavice. To není ona,
brání se Prokop, a vidí naduřelé dítě na křivičných nožkách, jež – jež – jež
se mu nestoudně nabízí! „Jdi pryč,“ křičí Prokop, a tu se mu zjeví pohozená
konev uprostřed záhonu povadlé a slimáky prolezlé kapusty a nemizí přes
všechno jeho úsilí.
Vtom tiše zazněl zvonek jako tiknutí ptáčka. Prokop se vrhl ke dveřím a
otevřel; na prahu stála dívka se závojem, tiskla k ňadrům balíček a
oddychovala. „To jste vy,“ řekl Prokop tiše a (neznámo proč) nesmírně dojat.
Dívka vešla, dotkla se ho ramenem; její vůně dechla na Prokopa trýznivým
opojením.
Zůstala stát uprostřed pokoje. „Prosím vás, nehněvejte se,“ mluvila tiše a
jakoby spěchajíc, „že jsem vám dala takové poslání. Vždyť ani nevíte, proč –
proč já – Kdyby vám to dělalo nějaké potíže –“
„Pojedu,“ vypravil ze sebe Prokop chraptivě.
Dívka upřela na něj zblízka své vážné, čisté oči. „Nemyslete si o mně nic
zlého. Já mám jenom strach, aby pan… aby váš přítel neudělal něco, co by
někoho… někoho jiného do smrti trápilo. Já mám k vám tolik důvěry… Vy ho
zachráníte, že?“
„Nesmírně rád,“ vydechl Prokop nějakým nesvým a rozechvěným hlasem; tak ho
opojovalo nadšení. „Slečno, já… co budete chtít…“ Odvracel oči; bál se, že
něco vybleptne, že je snad slyšet, jak mu bouchá srdce, a styděl se za svou
těžkopádnost.
I dívku zachvátil jeho zmatek; hrozně se zarděla a nevěděla kam s očima.
„Děkuju, děkuju vám,“ pokoušela se také jaksi nejistým hlasem, a silně mačkala
v rukou zapečetěný balíček. Nastalo ticho, jež působilo Prokopovi sladkou a
mučivou závrať. Cítil s mrazením, že dívka letmo zkoumá jeho tvář; a když k ní
náhle obrátil oči, viděl, že se dívá k zemi a čeká, připravena, aby snesla
jeho pohled. Prokop cítil, že by měl něco říci, aby zachránil situaci; místo
toho jen hýbal rty a chvěl se na celém těle.
Konečně pohnula dívka rukou a zašeptala: „Ten balíček –“ Tu zapomněl Prokop,
proč schovává pravou ruku za zády, a sáhl po tlusté obálce. Dívka zbledla a
couvla. „Vy jste poraněn,“ vyhrkla. „Ukažte!“ Prokop honem schovával ruku. „To
nic není,“ ujišťoval rychle, „to… to se mi jen trochu zanítila… zanítila
taková ranka, víte?“
Dívka, docela bledá, zasykla, jako by sama cítila tu bolest. „Proč nejdete k
lékaři?“ řekla prudce. „Vy nemůžete nikam jet! Já… já pošlu někoho jiného!“
„Vždyť už se to hojí,“ bránil se Prokop, jako by mu brali něco drahého.
„Jistě, to už je… skoro v pořádku, jen škrábnutí, a vůbec, to je nesmysl; proč
bych nejel? A pak, slečno, v takové věci… nemůžete poslat cizího člověka,
víte? Vždyť to už ani nebolí, hleďte,“ a zatřepal pravou rukou.
Dívka stáhla obočí přísnou soustrastí. „Vy nesmíte jet! Proč jste mi to
neřekl? Já – já – já to nedovolím! Já nechci –“
Prokop byl docela nešťasten. „Hleďte, slečno,“ spustil horlivě, „to jistě nic
není; já jsem na to zvyklý. Podívejte se, tady,“ a ukázal jí levou ruku, kde
mu scházel skoro celý malík a kloub ukazováčku naduřel uzlovitou jizvou. „To
už je takové řemeslo, víte?“ Ani nepozoroval, že dívka couvá s blednoucími rty
a dívá se na pořádný šrám jeho čela od oka k vlasům. „Udělá to prásk, a je to.
Jako voják. Zvednu se a běžím útokem dál, rozumíte? Nic se mně nemůže stát.
Nu, dejte sem!“ Vzal jí z ruky balíček, vyhodil do výše a chytil. „Žádná
starost, pane. Pojedu jako pán. Víte, já, já už jsem dávno nikde nebyl. Znáte
Ameriku?“
Dívka mlčela a hleděla na něho se zachmuřeným obočím.
„Ať si říkají, že mají nové teorie,“ drmolil Prokop horečně; „počkejte, já jim
to dokážu, až vyjdou mé výpočty. Škoda že tomu nerozumíte; já bych vám to
vyložil, vám věřím, vám věřím, ale jemu ne. Nevěřte mu,“ mluvil naléhavě,
„mějte se na pozoru. Vy jste tak krásná,“ vydechl nadšeně. „Tam nahoře já
nikdy s nikým nemluvím. Je to jen taková bouda z prken, víte? Haha, vy jste se
tak bála těch hlávek! Ale já vás nedám, o to nic; nebojte se ničeho. Já vás
nedám.“
Pohlížela na něho s očima rozšířenýma hrůzou. „Vy přece nemůžete odejet!“
Prokop zesmutněl a zmalátněl. „Ne, na to nesmíte dát, co mluvím. Povídal jsem
nesmysly, že? Já jsem jenom chtěl, abyste nemyslela na tu ruku. Abyste se
nebála. To už přešlo.“ – Přemohl své vzrušení, byl tuhý a zakaboněný samým
soustředěním. „Pojedu do Týnice a najdu Tomše. Dám mu ten balíček a řeknu, že
to posílá slečna, kterou zná. Je to tak správně?“
„Ano,“ řekla dívka váhavě, „ale vy přece nemůžete –“
Prokop se pokusil o prosebný úsměv; jeho těžká, rozjizvená tvář náhle docela
zkrásněla. „Nechte mne,“ řekl tiše, „vždyť je to – je to – pro vás.“
Dívka zamžikala očima; bylo jí skoro do pláče prudkým pohnutím. Mlčky kývla a
podávala mu ruku. Zvedl svou beztvarou levici; pohlédla na ni zvědavě a silně
ji stiskla. „Já vám tolik děkuju,“ řekla rychle, „sbohem!“
Ve dveřích se zastavila a chtěla něco říci; mačkala v ruce kliku a čekala –
„Mám mu… vyřídit… pozdrav?“ optal se Prokop s křivým úsměvem.
„Ne,“ vydechla a rychle na něj pohlédla. „Na shledanou.“
Dveře za ní zapadly. Prokop se na ně díval, bylo mu najednou na smrt těžko a
chabě, hlava se mu točila, a stálo ho nesmírné usilí, aby učinil jediný krok.


VI.

Na nádraží bylo mu čekati půldruhé hodiny. Sedl si na chodbě a chvěl se zimou.
V poraněné ruce mu pulsovala ukrutná bolest; zavíral oči, a tu se mu zdálo, že
ta bolavá ruka roste, že je veliká jako hlava, jako tykev, jako hrnec na
vyváření prádla, a že v celém jejím rozsahu palčivě cuká živé maso. Přitom mu
bylo mdlo k dávení a na čele mu ustavičně vyrážel studený pot úzkosti. Nesměl
se podívat na špinavé, poplivané, zablácené dlážky chodby, aby se mu nezvedal
žaludek. Vyhrnul si límec a polo snil, pomalu přemáhán nekonečnou
lhostejností. Zdálo se mu, že zas je vojákem a leží poraněn v širém poli; kde
– kde to pořád bojují? Tu zazněl mu do uší prudký zvon a někdo volal: „Týnice,
Duchcov, Moldava, nastupovat!“
Nyní už tedy sedí ve vagóně u okna a je mu nezřízeně veselo, jako by někoho
přelstil nebo někomu utekl; teď, holenku, už jedu do Týnice a nic mne nemůže
zadržet. Skoro se chechtal radostí, uvelebil se ve svém koutě a s náramnou
čilostí pozoroval své spolucestující. Naproti němu sedí nějaký krejčík s
tenkým krkem, hubená černá paní, pak člověk s divně bezvýraznou tváří; vedle
Prokopa strašně tlustý pán, kterému se nemůže nějak břicho vejít mezi nohy, a
snad ještě někdo, to už je jedno. Prokop se nesmí dívat z okna, protože mu to
dělá závrať. Ratata ratata ratata vybuchuje vlak, vše drnčí, bouchá, otřásá se
samou horlivostí spěchu. Krejčíkovi se klátí hlava napravo nalevo, napravo
nalevo; černá paní jaksi podivně a ztuhle hopkuje na místě, bezvýrazná tvář se
třese a kmitá jako špatný snímek ve filmu. A tlustý soused, to je kupa rosolu,
jež se houpe, otřásá, poskakuje nesmírně směšným způsobem. Týnice, Týnice,
Týnice, skanduje Prokop s údery kol; rychleji! rychleji! Vlak se ohřál samým
chvatem, je tu horko, Prokop se potí žárem; krejčík má nyní dvě hlavy na dvou
tenkých krcích, obě hlavy se třesou a narážejí na sebe, až to drnčí. Černá
paní výsměšně a urážlivě hopkuje na svém sedadle; tváří se naschvál jako
dřevěná loutka. Bezvýrazná tvář zmizela; sedí tam trup s rukama mrtvě
složenýma na klíně, ruce neživě poskakují, ale trup je bezhlavý.
Prokop sbírá všechny své síly, aby se na to pořádně podíval; štípe se do nohy,
ale nic platno, trup je dál bezhlavý a mrtvě se poddává otřesům vlaku.
Prokopovi je z toho děsně úzko; šťouchá loktem tlustého souseda, ale ten se
jen rosolovitě chvěje, a Prokopovi se zdá, že se mu to tlusté tělo bezhlase
chechtá. Nemůže se už na to dívat; obrací se k oknu, ale tam zničehonic vidí
lidskou tvář. Neví zprvu, co je na ní tak zarážejícího; pozoruje ji
vytřeštěnýma očima a poznává, že to je jiný Prokop, který na něho upírá oči s
děsivou pozorností. Co chce? zhrozil se Prokop. Proboha, nezapomněl jsem ten
balíček v Tomšově bytě? Hmatá honem po kapsách a najde obálku v náprsní kapse.
Tu se tvář v okně usmála a Prokopovi se ulevilo. Odvážil se dokonce pohlédnout
na bezhlavý trup; a vida, on si ten člověk jen přetáhl pověšený svrchník přes
hlavu a spí pod ním. Prokop by to udělal také, ale bojí se, aby mu někdo
nevytáhl z kapsy tu zapečetěnou obálku. A přece jde na něho spaní, je
nesnesitelně unaven; nikdy by si nedovedl představit, že je možno být tak
unaven. Usíná, vyrve se z toho vytřeštěně a opět usíná. Černá paní má jednu
hopkující hlavu na ramenou a druhou drží na klíně oběma rukama; a co se
krejčíka týče, sedí místo něho jen prázdné, beztělé šaty, z nichž čouhá
porcelánová palička. Prokop usíná, ale pojednou se z toho vytrhne v horlivé
jistotě, že je v Týnici; snad to někdo venku volal, neboť vlak stojí.
Vyběhl tedy ven a viděl, že už je večer; dva tři lidé vystupují na malinkém
blikajícím nádraží, za nímž je neznámá a mlhavá tma. Řekli Prokopovi, že do
Týnice musí jet poštou, je-li na ní ještě místo. Poštovní vůz, to byl jen
kozlík a za ním truhlík na zásilky; a na kozlíku už seděl pošťák a nějaký
pasažér.
„Prosím vás, vemte mne do Týnice,“ řekl Prokop.
Pošťák potřásl hlavou v nekonečném smutku. „Nejde,“ odpověděl po chvíli.
„Proč… jak to?“
„Není už místo,“ řekl pošťák zrale.
Prokopovi vstoupily do očí slzy samou lítostí. „Jak je tam daleko… pěšky?“
Pošťák účastně přemýšlel. „No, hodinu,“ řekl.
„Ale já… nemohu jít pěšky! Já musím k doktoru Tomšovi!“ protestoval Prokop
zdrcen.
Pošťák uvažoval. „Vy jste… jako… pacient?“
„Mně je zle,“ zamumlal Prokop; skutečně se chvěl slabostí a zimou.
Pošťák přemýšlel a potřásal hlavou. „Když to nejde,“ ozval se konečně.
„Já se vejdu, já… kdyby byl jen kousek místa, já…“
Na kozlíku ticho; jen pošťák se drbal ve vousech, až to chrastělo; pak neřekl
slova a slezl, dělal něco na postraňku a mlčky odešel do nádraží. Pasažér na
kozlíku se ani nepohnul.
Prokop byl tak vyčerpán, že si musel sednout na patník. Nedojdu, cítil
zoufale; zůstanu tady, až… až…
Pošťák se vracel z nádraží a nesl prázdnou bedničku. Nějak ji vpravil na
plošinu kozlíku a rozvážně ji pozoroval. „Tak si tam sedněte,“ řekl posléze.
„Kam?“ ptal se Prokop.
„No… na kozlík.“
Prokop se dostal na kozlík tak nadpřirozeně, jako by ho vynesly nebeské síly.
Pošťák zas dělal cosi na řemení, a teď sedí na bedničce s nohama visícíma dolů
a bere opratě. „Hý,“ povídá.
Kůň nic. Jenom se zachvěl.
Pošťák nasadil jakési tenké, hrdelní „rrr“. Kůň pohodil ocasem a pustil
hlasitý pšouk.
„Rrrrr.“
Pošta se rozjela. Prokop se křečovitě chytil nízkého zábradlíčka; cítil, že je
nad jeho síly udržet se na kozlíku.
„Rrrrr.“ Zdálo se, že ten vysoký hrčivý zpěv nějak galvanizuje starého koně.
Běžel kulhavě, pohazoval ocasem a při každém kroku pouštěl slyšitelné větry.
„Rrrrrrrr.“ Šlo to alejí holých stromů; byla černočerná tma, jen kmitavý
proužek světla z lucerny se smýkal po blátě. Prokop ztuhlými prsty svíral
zábradlíčko; cítil, že už vůbec nevládne svému tělu, že nesmí spadnout, že
bezmezně slábne. Nějaké osvětlené okno, alej, černá pole. „Rrrr.“ Kůň vytrvale
pšukal a klusal pleta nohama toporně a nepřirozeně, jako by byl už dávno
mrtev.
Prokop se úkosem podíval na svého spolucestujícího. Byl to děda s krkem
ovázaným šálou; pořád něco žvýkal, překusoval, žmoulal a zase vyplivoval. A tu
si Prokop vzpomněl, že tu podobu už viděl. Byla to ta ohavná tvář ze sna, jež
skřípala vyžranými zuby, až se drtily, a pak je po kouskách vyplivovala. Bylo
to divné a strašné.
„Rrrrr.“ Silnice se obrací, motá se do kopce a zase dolů. Nějaký statek, je
slyšet psa, člověk jde po silnici a povídá „dobrý večer“. Domků přibývá, jde
to do kopce. Pošta zatáčí, vysoké „rrrr“ náhle ustane a kůň se zastaví.
„Tak tady bydlí doktor Tomeš,“ povídá pošťák.
Prokop chtěl něco říci, ale nemohl; chtěl se pustit zábradlí, ale nešlo to,
protože mu prsty křečovitě ztuhly.
„No, už jsme tady,“ povídá pošťák znovu. Ponenáhlu křeče povolí a Prokop slézá
z kozlíku, chvěje se na celém těle. Jakoby popaměti otvírá vrátka a zvoní u
dveří. Uvnitř zuřivý štěkot, a mladý hlas volá: „Honzíku, ticho!“ Dveře se
otevrou, a stěží hýbaje jazykem ptá se Prokop: „Je pan doktor doma?“
Chvilku ticho; pak řekl mladý hlas: „Pojďte dál.“
Prokop stojí v teplé světnici; na stole je lampa a večeře, voní to bukovým
dřívím. Starý pán s brejličkami na čele vstává od svého talíře, jde k
Prokopovi a povídá: „Tak copak vám schází?“
Prokop se mračně upomínal, co tu vlastně chce. „Já… totiž…,“ začal, „je váš
syn doma?“
Starý pán se pozorně díval na Prokopa. „Není. Co vám je?“
„Jirka… Jiří,“ m ručel Prokop, „já jsem… jeho přítel a nesu mu… mám mu dát…“
Lovil v kapse zapečetěnou obálku. „Je to… důležitá věc a… a…“
„Jirka je v Praze,“ přerušil ho starý pán. „Člověče, sedněte si aspoň!“
Prokop se nesmírně podivil. „Vždyť říkal… říkal, že jede sem. Já mu musím
dát…“ Podlaha se pod ním zakymácela a počala se naklánět.
„Aničko, židli,“ křikl starý pán podivným hlasem.
Tu ještě zaslechl Prokop tlumený výkřik a poroučel se na zem. Zalila ho
nesmírná temnota, a pak již nebylo nic.


VII.

Nebylo nic; jen jako kdyby se časem protrhly mlhy, zjevil se vzorek malované
stěny, řezaná římsa skříně, cíp záclony či frýzek stropu; nebo se naklonila
nějaká tvář jakoby nad otvorem studně, ale nebylo vidět jejích rysů. Něco se
dělo, někdo časem svlažil horké rty nebo pozvedal bezvládné tělo, ale vše
mizelo v plynoucích útržcích snění. Byly to krajiny, kobercové vzory,
diferenciální počty, ohnivé koule, chemické formule; jen časem něco vyplulo
navrch a stalo se na okamžik jasnějším snem, aby se to vzápětí zas rozplynulo
v širokotokém bezvědomí.
Konečně přišla chvíle, kdy procitl; viděl nad sebou teplý a bezpečný strop se
štukovým frýzkem; našel očima své vlastní hubené, mrtvě bílé ruce na květované
přikrývce; za nimi objevil pelest postele, skříň a bílé dveře: vše nějak milé,
tiché a už známé. Neměl ponětí, kde je; chtěl o tom uvažovat, ale měl nemožně
slabou hlavu, vše se mu opět počalo mást, i zavřel oči a odpočíval v odevzdané
chabosti.
Dveře tichounce zavrzly. Prokop otevřel oči a posadil se na posteli, jako by
ho něco zvedlo. A ono u dveří stojí děvče, vytáhlé nějak a světlé, má
jasňoučké oči náramně udivené, ústa pootevřená překvapením a tiskne k prsoum
bílé pláténko. Nehýbe se rozpačitá, mrká dlouhými řasami a její růžový čumáček
se počíná nejistě, plaše usmívat.
Prokop se zachmuřil; usilovně hleděl něco říci, ale měl v hlavě docela
prázdno; hýbal nehlasně rty a pozoroval dívku jaksi přísnýma a vzpomínavýma
očima.
„Gúnúmai se, anassa,“ splynulo mu náhle a bezděčně se rtů, „theos ny tis é
brotos essi? Ei men tis theos essi, toi úranon euryn echúsin, Artemidi se egó
ge, Dios kúré megaloio, eidos te megethos te fyén t’anchista eďskó.“ A dále,
verš za veršem, řinulo se božské pozdravení, jímž Odysseus oslovil Nausikau.
„Proboha prosím tě, paní! Jsi božstvo či smrtelný člověk? Jestližes některá z
bohyň, co sídlí na nebi širém, s Artemidou bych já, jež velkého Dia je dcera,
krásou a vzrůstem těla i velkostí nejspíš tě srovnal. Jsi-li však některá z
lidí, co mají na zemi sídlo, třikrát blaženi jsou tvůj otec i velebná matka,
třikrát blaženi bratři, neb jistě jim nadmíru srdce pokaždé rozkoší blahou se
pro tebe rozhřívá v hrudi, kdykoli zří, jak takový květ jde do kola k tanci.“
Dívka bez hnutí, jako zkamenělá, naslouchala tomuto po. zdravu v neznámé řeči;
a na jejím hladkém čele bylo tolik zmatku, její oči tak dětsky a polekaně
mžikaly, že Prokop zdvojnásobil horlivost Odyssea na břeh vyvrženého, sám jer
nejasně chápaje smysl slov.
„Keinos ďau peri kéri makartatos,“ odříkával rychle. „Avšak nad jiné ten se
pocítí blaženým v srdci, který zvítězí dary a tebe si odvede domů, neboť dosud
nikdy jsem takého člověka nezřel ze všech mužů ni žen; já s úžasem na tebe
hledím.“
Sebas m’echei eisoroónta. Děvče se silně zardělo, jako by rozumělo pozdravu
řeckého hrdiny; neobratný a líbezný zmatek jí vázal údy, a Prokop, spínaje
ruce na pokrývce, mluvil, jako by se modlil.
„Déló dé pote,“ pokračoval spěšně, „jenom na Délu jednou, blíž oltáře jasného
Foiba, palmový mladý strůmek jsem viděl ze země růsti, – neboť i tam jsem
přišel a množství lidu šlo se mnou na cestě té, z níž trampoty zlé mi vzejíti
měly. Tam jsem právě tak stál, pln úžasu, když jsem jej viděl, dlouho, vždyť
takový kmen se nezrodil ze země dosud. Tak teď tobě se divím a žasnu a bojím
se hrozně dotknout se kolenou tvých, ač velký smutek mě tísní.“
Deidia ďainós: ano, bál se hrozně, ale i dívka se bála a tiskla k prsoum bílé
prádlo a neodvracela očí z Prokopa, jenž chvátal vypovědět svou trýzeň:
„Včera, až v dvacátý den, jsem ušel třpytnému moři, do doby té jsem vlnou byl
hnán a prudkými větry od výspy Ógygie, teď sem mě zas uvrhlo božstvo, abych tu
též snad zakusil strast, vždyť sotva se, tuším, skončí, a množství běd mi
bohové přisoudí ještě.“
Prokop těžce vzdychl a pozvedl úděsně vyhublé ruce. „Alla, anass‘, eleaire!
Avšak slituj se, paní, vždyť vytrpěv útrapy mnohé, nejdřív přišel jsem k tobě
– z těch druhých nikoho neznám lidí, co v krajině té a v městě své obydlí
mají. Do města cestu mi ukaž, dej roucho, bych tělo si zakryl, jestližes
vzala, sem jdouc, snad nějaký na prádlo obal.“
Nyní se dívčí tvář poněkud vyjasnila, vlahé rty se pootevřely; snad Nausikaá
promluví, ale Prokop chtěl jí ještě požehnati za ten obláček líbezného
soucitu, kterým růžovělo její líčko. „Soi de theoi tosa doien, hosa fresi sési
menoinás: bozi pak račte ti dát, čeho ve své mysli si žádáš, muže i dům, a
přidejtež vám i svorného ducha, vzácný to dar, – vždyť lepšího nic ni krasšího
není, než když smýšlením svorni svou domácnost společně vedou žena i muž, jak
odpůrcům v žal, tak na radost velkou všechněm příznivcům svým, a nejvíc to
pocítí sami.“ [* Překlad O. Vaňorného (1921)]
Poslední slova Prokop už skoro jen dýchal; sám stěží rozuměl tomu, co
odříkává, vytékalo to plynně a bez vůle z nějakého neznámého kouta paměti;
bylo tomu skoro dvacet let, co se jakžtakž probíral sladkou melodií šestého
zpěvu. Působilo mu až fyzickou úlevu nechávat to volně odtékat; dělalo se mu v
hlavě lehčeji a jasněji, bylo mu skoro blaženě v té plihé a libé slabosti, a
tu se mu zachvěl na rtech rozpačitý úsměv.
Dívka se usmála, pohnula sebou a řekla: „Nu tak?“ Udělala krůček blíž a dala
se do smíchu. „Co jste to povídal?“
„Já nevím,“ děl Prokop nejistě.
Tu se rozlétly nedovřené dveře a do pokoje vrazilo něco malého a chundelatého,
kviklo radostí a skočilo Prokopovi na postel.
„Honzíku,“ křikla dívka polekaně, „jdeš dolů!“ Ale psisko už olízlo Prokopovu
tvář a v náruživé radosti se zachumlávalo do přikrývek. Prokop si sáhl na
tvář, aby se otřel, a s úžasem pocítil pod rukou plnovous. „Co-copak,“ koktal
a umlkl údivem. Psisko bláznilo; kousalo s překypující něhou Prokopovy ruce,
pištělo, funělo, a tumáš! mokrou mordou se mu dostalo až na prsa.
„Honzíku,“ křičela dívka, „ty jsi blázen! Necháš pána!“ Přiběhla k posteli a
vzala psíka do náruče. „Bože, Honzíku, ty jsi hlupák!“
„Nechte ho,“ žádal Prokop.
„Vždyť máte bolavou ruku,“ namítalo děvče s velikou vážností, tisknouc k
prsoum zápasícího psa.
Prokop se podíval nechápavě na svou pravici. Od palce přes dlaň táhla se
široká jizva, pokrytá novou, tenoučkou, červenou kožkou příjemně svědící.
„Kde… kde to jsem?“ podivil se.
„U nás,“ řekla s náramnou samozřejmostí, jež Prokopa ihned uspokojila. „U
vás,“ opakoval s úlevou, ač neměl ponětí, kde to je. „A jak dlouho?“
„Dvacátý den. A pořád –,“ chtěla něco říci, ale spolkla to. „Honzík spával s
vámi,“ dodala spěšně a zarděla se neznámo proč, chovajíc psa jako malé dítě.
„Víte o tom?“
„Nevím,“ vzpomínal Prokop. „Copak jsem spal?“
„Pořád,“ vyhrkla. „Už jste se mohl vyspat.“ Tu postavila psa na zem a
přiblížila se k posteli. „Je vám líp?… Chtěl byste něco?“
Prokop zakroutil hlavou; nevěděl o ničem, co by chtěl. „Kolik je hodin?“ ptal
se nejistě.
„Deset. Já nevím, co smíte jíst; až přijde tati… Tati bude tak rád… Chtěl
byste něco?“
„Zrcadlo,“ řekl Prokop váhavě.
Dívka se zasmála a vyběhla. Prokopovi hučelo v hlavě; pořád se hleděl
rozpomenout a pořád mu vše unikalo. A už je tu děvče, něco povídá a podává mu
zrcátko. Prokop chce zvednout ruku, ale bůhsámví proč to nejde; děvče mu
vkládá držadlo mezi prsty, ale zrcátko padá na pokrývku. Tu zbledlo děvče,
nějak se znepokojilo a samo mu nastavilo zrcadlo k očím. Prokop se dívá, vidí
docela zarostlé tváře a obličej skoro neznámý; hledí a nemůže pochopit, a tu
se mu roztřásly rty.
„Lehněte si, hned si zas lehněte,“ káže mu drobounký hlásek skoro plačící, a
rychlé ruce mu nastavují podušku. Prokop se sváží naznak a zavírá oči; jen
chvilinku si zdřímnu, myslí si, a udělalo se libé, hluboké ticho.


VIII.

Někdo ho zatahal za rukáv. „Nu, nu,“ povídá ten někdo, „už bychom nemuseli
spát, co?“ Prokop otevřel oči a viděl starého pána, má růžovou pleš a bílou
bradu, zlaté brejličky na čele a náramně čilý koukej. „Už nespěte, velectěný,“
povídá, „už toho je dost; nebo se probudíte na onom světě.“
Prokop si chmurně prohlížel starého pána; chtělo se mu totiž dřímat. „Co
chcete?“ ozval se vzdorovitě. „A… s kým mám tu čest?“
Starý pán se dal do smíchu. „Prosím, doktor Tomeš. Vy jste mne neráčil dosud
vzít na vědomí, co? Ale nic si z toho nedělejte. Tak co, jak se máme?“
„Prokop,“ ozval se nemocný nevlídně.
„Tak, tak,“ povídal doktor spokojeně. „A já jsem si myslel, že jste Šípková
Růženka. A teď, pane inženýre,“ řekl čile, „se na vás musíme podívat. No,
neškareďte se.“ Vyeskamotoval mu z podpaží teploměr a libě zachrochtal.
„Třicet pět osm. Človíčku, vy jste jako moucha. Musíme vás nakrmit, co?
Nehýbejte se.“
Prokop cítil na prsou hladkou pleš a studené ucho, jak mu jezdí od ramene k
rameni, od břicha k hrdlu za povzbuzujícího broukání.
„No, sláva,“ řekl konečně doktor a nasadil si brejle na oči. „Napravo vám to
drobátko rachotí, a srdce – no, to se urovná, že?“ Naklonil se k Prokopovi,
drbal ho prsty ve vlasech a přitom mu palcem zvedal a zase zatlačoval oční
víčka. „Nespat už, víme?“ mluvil a přitom mu něco zkoumal na zorničkách.
„Dostaneme knížky a budeme číst. Sníme něco, vypijeme skleničku vína a –
nehýbejte se! Já vás neukousnu.“
„Co mně je?“ ptal se Prokop nesměle.
Doktor se vztyčil. „No, nic už. Poslechněte, kde jste se tady vzal?“
„Kde tady?“
„Tady, v Týnici. Sebrali jsme vás na podlaze a… Odkud jste, člověče, přišel?“
„Já nevím. Z Prahy, ne?“ vzpomínal Prokop.
Doktor potřásl hlavou. „Vlakem z Prahy! Se zápalem mozkových blan! Měl jste
rozum? Víte, co to vůbec je?“
„Co?“
„Meningitis. Spací forma. A k tomu zápal plic. Čtyřicet celých, he? Kamaráde,
s něčím takovým se nejezdí na výlety. A víte, že – nu, ukažte honem pravou
ruku!“
„To… to bylo jen škrábnutí,“ hájil se Prokop.
„Pěkné škrábnutí. Otrava krve, rozumíte? Až budete zdráv, řeknu vám, že jste
byl… že jste byl osel. Odpusťte,“ řekl s důstojným rozhořčením, „málem bych
byl řekl něco horšího. Vzdělaný člověk, a neví, že toho má v sobě na trojí
exitus! Jak jste se vůbec mohl držet na nohou?“
„Já nevím,“ šeptal Prokop zahanbeně.
Doktor chtěl hubovat dál, ale zavrčel jen a mávl rukou. „A jak se cítíte?“
začal přísně. „Trochu pitomý, ne? Žádná paměť, co? A tady, tady nějak,“ ťukal
si na čelo, „nějaký slabý, že?“
Prokop mlčel.
„Tak tedy, pane inženýre,“ spustil doktor. „Z toho si nic nedělat. Nějaký
čásek to potrvá, co? Rozumíte mi? Nesmíte si namáhat hlavu. Nemyslet. To se
vrátí… po kouskách. Jen přechodná porucha, slabá amence, rozumíte mi? To
přejde samo od sebe, co? Rozumíte mi?“
Doktor křičel, potil se a rozčiloval se, jako by se hádal s hluchoněmým.
Prokop se na něj pozorně díval, a ozval se klidně: „Já tedy zůstanu
slabomyslný?“
„Ale ne, ne,“ rozčiloval se doktor. „Naprosto vyloučeno. Ale prostě… po
nějakou dobu… porucha paměti, roztržitost, únava a takové ty příznaky,
rozumíte mi? Poruchy v koordinaci, chápete? Odpočívat. Klid. Nic nedělat.
Velectěný, děkujte pánubohu, že jste to vůbec přečkal.“
„Přečkal,“ ozval se po chvíli a radostně zatroubil do kapesníku. „Poslechněte,
takový případ jsem ještě neměl. Vy jste sem přišel pěkně v deliriu, praštil
jste sebou na zem, a finis, poroučím se vám. Co jsem měl s vámi dělat? Do
nemocnice je daleko, a holka nad vámi tento, brečela… a vůbec, přišel jste
jako host k… Jirkovi, k synovi, no ne? Tak jsme si vás tu nechali, rozumíte
mi? Nu, nám to nevadí. Ale takového zábavného hosta jsem ještě neviděl. Dvacet
dní prospat, pěkně děkuju! Když vám kolega primář řezal ruku, ani jste se
neráčil probudit, co? Tichý pacient, namouduši. No, to už je jedno. Jen když
jste z toho venku, člověče.“ Doktor se plácl hlučně do stehna. „U čerta,
nespěte už! Pane, hej, pane, mohl byste usnout nadobro, slyšíte? U všech
všudy, hleďte se trochu přemáhat! Nechte toho, slyšíte?“
Prokop chabě kývl; cítil, že se nějaké závoje přetahují mezi ním a
skutečností, že se vše obestírá, kalí a tichne.
„Andulo,“ slyšel zdáli rozčilený hlas, „víno! dones víno!“ Nějaké rychlé
kroky, hovor jakoby pod vodou, a chladivá chuť vína mu stékala do hrdla.
Otevřel oči a viděl nad sebou skloněné děvče. „Nesmíte spát,“ povídá děvče
rozechvěně, a její předlouhé řasy mžikají, jako když srdce tluče.
„Já už nebudu,“ omlouvá se Prokop pokorně.
„To bych si vyprosil, velectěný,“ lomozil doktor u pelestě. „Přijede sem z
města primář extra na konzultaci; ať vidí, že my felčaři venku taky něco
umíme, no ne? Musíte se pěkně držet.“ S neobyčejnou obratností zvedl Prokopa a
shrnul mu za záda polštáře. „Tak, teď bude pán sedět; a spaní si nechá až po
obědě, že? Já musím do ordinace. A ty, Ando, si tady sedni a něco žvaň; jindy
ti huba jede jako trakař, co? A kdyby chtěl spát, zavolej mne; já už si to s
ním vyřídím.“ Ve dveřích se obrátil a zavrčel: „Ale… mám radost, rozumíte? Co?
Tak pozor!“
Prokopovy oči se svezly na dívku. Seděla opodál, ruce v klíně, a při bohu
nevěděla, o čem mluvit. Tak, teď zvedla hlavu a pootevřela ústa; slyšme, co z
ní vyletí; ale zatím se jenom zastyděla, spolkla to a sklopila hlavu ještě
níž; je vidět jen dlouhé řasy, jak se chvějí nad líčkem.
„Tati je tak prudký,“ ozvala se konečně. „On je tak zvyklý křičet… vadit se… s
pacienty…“ Látka jí bohužel došla; zato – jako na zavolanou – ocitla se jí v
prstech zástěra a nechala se dlouho a všelijak zajímavě skládat, za pozorného
mžikání ohnutých řas.
„Co to řinčí?“ optal se Prokop po delší době.
Obrátila hlavu k oknu; má pěkné světlé vlasy, jež jí ozařují čelo, a šťavnaté
světélko na vlhké puse. „To jsou krávy,“ povídá s úlevou. „Tam je panský dvůr,
víte? Tenhle dům taky patří k panství. Tati má koně a kočárek… Jmenuje se
Fricek.“
„Kdo?“
„Ten kůň. Vy jste nebyl nikdy v Týnici, že? Tady nic není. Jen aleje a pole…
Dokud byla živa maminka, tak tu bylo veseleji; to sem jezdil náš Jirka… Už tu
nebyl přes rok; pohádal se s tatim a… ani nepíše. Ani se o něm u nás nesmí
mluvit – Vídáte ho často?“
Prokop rozhodně zavrtěl hlavou.
Děvče vzdychlo a zamyslilo se. „On je… já nevím. Takový divný. Jen tu chodil s
rukama v kapsách a zíval… Já vím, že tu nic není; ale přec… Tati je taky rád,
že jste zůstal u nás,“ zakončila rychle a trochu nesouvisle.
Někde venku se chraptivě a směšně rozkřikl mladý kohoutek. Najednou se tam
dole strhlo jakési slepičí rozčilení, bylo slyšet divoké „ko-ko-ko-“ a vítězně
kvikající štěkot psiska. Děvče vyskočilo. „Honzík honí slepice!“ Ale hned si
zase sedla, odhodlána ponechat slípky jejich osudu. Bylo příjemné a jasné
ticho.
„Já nevím, o čem povídat,“ řekla po chvíli s nejkrásnější prostotou. „Já vám
přečtu noviny, chcete?“
Prokop se usmál. A už tu byla s novinami a pustila se odvážně do úvodníku.
Finanční rovnováha, státní rozpočet, nekrytý úvěr… Líbezný a nejistý hlásek
odříkával klidně ty nesmírně vážné věci, a Prokopovi, jenž naprosto
neposlouchal, bylo lépe, než kdyby hluboce spal.


IX.

Nyní už smí Prokop na nějakou hodinku denně vylézt z postele; dosud táhne nohy
všelijak a bohužel není s ním mnoho řeči; říkejte si mu co chcete, většinou
odpoví nějak skoupě a přitom se omlouvá plachým úsměvem.
Dejme tomu v poledne – je teprve začátek dubna – sedává v zahrádce na lavičce;
vedle něho ježatý teriér Honzík se směje na celé kolo pod svými mokrými
fořtovskými vousy, neboť je zřejmě pyšný na svou funkci společníka, a samou
radostí se oblízne a mhouří oči, když ho zjizvená Prokopova levička pohladí po
teplé huňaté hlavě. V tu hodinu obyčejně doktor vyběhne z ordinace, čepička mu
sem tam jezdí po hladké pleši, sedne na bobek a sází zeleninu; tlustými
krátkými prsty rozmílá hrudky prsti a pozorně vystýlá lůžko mladých klíčků. Co
chvíli se začne rozčilovat a bručí; zapíchl někde do záhonku svou lulku a
nemůže ji najít. Tu se Prokop zvedne a s divinací detektiva (neboť čte v
posteli detektivky) zamíří rovnou k ztracené faječce. Čehož Honzík užije k
tomu, aby se hlučně otřepal.
V tu hodinu chodívá Anči (neboť tak a nikoliv Andula si přeje být jmenována)
zalévat tatínkovy záhony. V pravé ruce nese konev, levá plave ve vzduchu;
stříbrná prška šumí do mladé hlíny, a naskytne-li se nablízku Honzík, dostane
ji na zadek nebo na pitomou veselou hlavu; tu zoufale kvikne a hledá ochranu u
Prokopa.
Celé ráno se trousí do ordinace pacienti. Chrchlají v čekárně a mlčí, každý
mysle jen na své utrpení. Někdy se ozve z ordinace strašný křik, když doktor
tahá zub nějakému kloučeti. Tu se zase Anči v panice zachrání k Prokopovi,
bledá a zrovna bez sebe, úzkostně mžiká krásnými řasami a čeká, až strašná
událost přejde. Konečně kluk ubíhá ven s táhlým vytím, a Anči nějak nešikovně
zamlouvá svou útlocitnou zbabělost.
Ovšem něco jiného je, když před doktorovým domem zastaví vůz vystlaný slámou a
dva strejci opatrně vynášejí po schodech těžce raněného člověka. Má rozdrcenou
ruku nebo zlomenou nohu nebo hlavu roztříštěnou kopytem; studený pot se mu
řine po hrozně bledém čele, a tiše, s hrdinným sebepřemáháním sténá. Na celý
dům lehne tragické ticho; v ordinaci se bez hluku odehrává něco těžkého,
tlustá veselá služka chodí po špičkách, Anči má oči plné slz a prsty se jí
třesou. Doktor vrazí do kuchyně, s křikem žádá rum, víno nebo vodu, a
dvojnásobnou hrubostí zakrývá mučivý soucit. A ještě celý den potom nemluví a
vzteká se a bouchá dveřmi.
Ale je také veliký svátek, slavný výroční trh venkovské doktořiny: očkování
dětí. Sta maminek houpá své bečící, řvoucí, spící uzlíčky, je toho plná
ordinace, chodba, kuchyně i zahrádka; Anči je jako blázen, chtěla by chovat,
houpat a převíjet všechny ty bezzubé, uřvané, ochmýřené děti v nadšeném
záchvatu kybelického mateřství. I starému doktorovi se nějak okázaleji svítí
pleš, od rána chodí bez brejlí, aby nepolekal ty haranty, a oči mu plavou
únavou a radostí.
Jindy uprostřed noci rozčileně zařinčí zvonek. Pak bručí ve dveřích nějaké
hlasy, doktor hubuje a kočí Jozef musí zapřahat. Někde ve vsi za svítícím
okénkem přichází na svět nový člověk. Až ráno se doktor vrací, unavený, ale
spokojený, a na deset kroků smrdí karbolem; ale takhle ho má Anči nejraději.
Pak jsou tu ještě jiné osobnosti: tlustá řehtavá Nanda v kuchyni, která po
celý den zpívá a řinčí a ohýbá se smíchem. Dále vážný kočí Jozef s visutými
kníry, historik; čte pořád dějepisné knížky a rád vykládá dejme tomu o
husitských válkách nebo o historických tajemnostech kraje. Dále panský
zahradník, náramný holkář, který denně zaskočí do doktorovy zahrady, očkuje mu
růže, stříhá keře a uvádí Nandu do nebezpečných záchvatů smíchu. Dále zmíněný
chlupatý a rozjařený Honzík, jenž provází Prokopa, honí blechy a slepice a
zmíry rád jezdí na kozlíku doktorova kočárku. Fric, to je starý rap trochu
šedivějící, přítel králíků, rozšafný a dobrosrdečný kůň; pohladit jeho teplé a
citlivé nozdry, to je prostě vrchol příjemnosti. Dále brunátný adjunkt ze
dvora, zamilovaný do Anči, která si z něho ve spojení s Nandou ukrutně střílí.
Ředitel ze dvora, starý lišák a zloděj, jenž chodí s doktorem hrát v šachy;
doktor se rozčiluje, zuří a prohrává. A jiné místní osobnosti, mezi nimiž
neobyčejně nudný a politicky interesovaný civilní geometr otravuje Prokopa
právem kolegiality.
Prokop mnoho čte nebo se tváří, jako by četl. Jeho zjizvená, těžká tvář mnoho
nepovídá, zejména ne o zoufalém a tajném zápasu s porouchanou pamětí. Zvláště
poslední pracovní léta mnoho utrpěla; nejjednodušší vzorce a procesy jsou ty
tam, a na okraji knížek si píše Prokop kusé formule, které se mu vynořují v
hlavě, když na ně nejméně myslí. Pak se sebere a jde hrát s Anči kulečník;
neboť je to hra, při které se mnoho nemluví. I na Anči padá jeho kožená a
neproniknutelná vážnost; hraje soustředěně, míří s přísně staženým obočím, ale
když koule zamíří naschvál jinam, otevře údivem ústa a mokrým jazejčkem jí
ukazuje správnou cestu.
Večery u lampy. Nejvíc toho napovídá doktor, nadšený přírodovědec bez
jakýchkoliv znalostí. Zejména jej okouzlují poslední záhady světa:
radioaktivita, nekonečnost prostoru, elektřina, relativita, původ hmoty a
stáří lidstva. Je zapřisáhlý materialista, a právě proto cítí tajemnou a
sladkou hrůzu neřešitelných věcí. Někdy se Prokop nezdrží a opravuje
büchnerovskou naivitu jeho názorů. Tu starý pán naslouchá přímo pobožně a
počíná si Prokopa nesmírně vážit, zejména tam, kde mu přestává rozumět,
řekněme takhle o rezonančním potenciálu nebo teorii kvant. Anči, ta prostě
sedí opírajíc se bradou o stůl; je sice na tuto pozici už trochu veliká, ale
patrně od maminčiny smrti zapomněla dospívat. Ani nemrká a dívá se velkýma
očima z táty na Prokopa a vice versa.
A noci, noci jsou pokojné a širé jako všude venku. Chvílemi zařinčí z kravína
řetězy, chvílemi se blízko nebo daleko rozštěkají psi; po nebi se mihne
padající hvězda, jarní déšť zašumí v zahradě nebo stříbrným zvukem odkapává
osamělá studna. Čirý, hlubinný chlad vane otevřeným oknem, a člověk usíná
požehnaným spánkem bez vidin.


X.

Nuže, bylo lépe; den za dnem se Prokopovi vracel život drobnými krůčky. Cítil
jen malátnost hlavy, bylo mu stále trochu jako ve snách. Nezbývalo než
poděkovat doktorovi a jeti po svém. Chtěl to ohlásit jednou po večeři, ale
zrovna všichni mlčeli jako zařezaní. A pak vzal starý doktor Prokopa pod paží
a dovedl si ho do ordinace; po nějakém okolkování vyhrkl s rozpačitou
hrubostí, že jako Prokop nemusí odjíždět, ať raději odpočívá, že nemá ještě
vyhráno, a vůbec ať si tu zůstane a dost. Prokop se matně bránil; faktum ovšem
bylo, že se ještě necítil v sedle a že se poněkud rozmazlil. Zkrátka o odjezdu
nebylo zatím řeči.
Vždy odpoledne se doktor zavíral v ordinaci. „Přijďte si někdy ke mně sednout,
co?“ řekl Prokopovi mimochodem. Tak tedy ho Prokop zastihl u všelijakých
lahviček a kelímků a prášků. „Víte, tady v místě není hapatyka,“ vysvětloval
doktor, „já musím sám dělat léky.“ A třesoucími se tlustými prsty dozoval
nějaký prášek na ručních vážkách. Měl nejistou ruku, váhy se mu houpaly a
točily; starý pán se rozčiloval, funěl a potil se na nose drobnými krůpějkami.
„Když na to pořádně nevidím,“ zamlouval stáří svých prstů. Prokop se chvíli
díval, pak neřekl nic a vzal mu vážky z ruky. Klep, klep, a prášek byl na
miligram odvážen. A druhý, třetí prášek. Citlivé vážky jen tančily v
Prokopových prstech. „Ale koukejme, koukejme,“ divil se doktor a s úžasem
sledoval Prokopovy ruce, rozbité, uzlovité, s netvornými klouby, ulámanými
nehty a krátkými pahýly místo několika prstů. „Človíčku, vy máte šikovnost v
těch rukou!“ Za chvíli už Prokop roztíral nějakou masť, odměřoval kapky a
nahříval zkumavky. Doktor zářil a nalepoval viněty. Za půl hodiny byl hotov s
celou lékárnou, a ještě tu byla hromada prášků do zásoby. A po několika dnech
Prokop už zběžně četl doktorovy recepty a bez řečí mu dělal magistra. Bon.
Kdysi kvečeru se dloubal doktor na zahradě v kyprém záhonku. Najednou strašná
rána v domě, a hned nato se s řinkotem sypalo sklo. Doktor se vrhl do domu a
na chodbě se srazil s uděšenou Anči. „Co se stalo?“ volal. „Já nevím,“
vypravilo ze sebe děvče. „To v ordinaci…“ Doktor běžel do ordinace a viděl
Prokopa, jak na všech čtyřech sbírá na podlaze střepy a papíry.
„Co jste tu dělal?“ rozkřikl se doktor.
„Nic,“ řekl Prokop a provinile vstával. „Praskla mně zkumavka.“
„Ale co u všech všudy,“ hromoval doktor a zarazil se: z Prokopovy levice
čurkem stékala krev. „Copak vám to utrhlo prst?“
„Jen škrábnutí,“ protestoval Prokop a schovával levičku za zády.
„Ukažte,“ křikl starý doktor a táhl Prokopa k oknu. Půl prstu viselo jen na
kůži. Doktor se hnal ke skříni pro nůžky, a v otevřených dveřích zahlédl Anči
na smrt bledou. „Co tu chceš?“ spustil. „Marš odtud!“ Anči se nehnula; tiskla
ruce k prsoum a vypadala co nejslibněji na omdlení.
Doktor se vrátil k Prokopovi; nejdřív dělal něco s vatičkou a pak cvakly
nůžky. „Světlo,“ křikl na Anči. Anči se vrhla k vypínači a rozsvítila. „A
nestůj tady,“ hřmotil starý pán a koupal jehlu v benzínu. „Co tu máš co dělat?
Podej sem nitě!“ Anči skočila ke skříni a podala mu ampulku s nitěmi. „A teď
jdi!“
Anči se podívala na Prokopova záda a udělala něco jiného; přistoupila blíž,
chopila oběma dlaněma tu poraněnou ruku a podržela ji. Doktor si zrovna myl
ruce; obrátil se k Anči a chtěl vybuchnout; místo toho zabručel: „Tak, teď drž
pevně! A víc u světla!“
Anči zamhouřila oči a držela. Když nebylo slyšet nic než doktorovo supění,
odvážila se zvednout oči. Dole, kde pracoval otec, to bylo krvavé a ošklivé.
Pohlédla honem na Prokopa; měl odvrácenou tvář, a jeho víčkem cukala bolest.
Anči trnula a polykala slzy a dělalo se jí nanic.
Zatím Prokopova ruka narůstala: spousta vaty, Billrothův batist a snad
kilometr fáče pořád navíjeného; konečně z toho bylo něco ohromného bílého.
Anči držela, kolena se jí třásla, zdálo se jí, že ta strašná operace nikdy
nebude u konce. Najednou se jí zatočila hlava, a pak slyšela, jak otec povídá:
„Na, vypij to honem!“ Otevřela oči a shledala, že sedí v ordinační sesli, že
tati jí podává skleničku s něčím, za ním že stojí Prokop, usmívá se a chová na
prsou zavázanou ruku vypadající jako obrovské poupě. „Tak to vypij,“ naléhal
doktor a jen cenil zuby. Spolkla to tedy a rozkuckala se; byl to vražedný
koňak.
„A teď vy,“ řekl doktor a podal skleničku Prokopovi. Prokop byl trochu bledý a
statečně čekal, že dostane vynadáno. Nakonec se napil doktor, odchrchlal a
spustil: „Tak co jste tu vlastně prováděl?“
„Pokus,“ řekl Prokop s křivým úsměvem provinilce.
„Co? Jaký pokus? S čím pokus?“
„Jen tak. Jen – jen – jde-li něco udělat z chloridu draselnatého.“
„Co udělat?“
„Třaskavina,“ šeptal Prokop v pokoře hříšníka.
Doktor se svezl očima na jeho ofáčovanou ruku. „A to se vám vyplatilo,
člověče! Ruku vám to mohlo utrhnout, co? Bolí? Ale dobře na vás, patří vám
to,“ prohlašoval krvelačně.
„Ale tati,“ ozvala se Anči, „nech ho teď!“
„A co ty tu máš co dělat,“ zavrčel doktor a pohladil ji rukou páchnoucí
karbolem a jodoformem.
Nyní doktor nosil klíč od ordinace v kapse. Prokop si objednal balík učených
svazků, chodil s rukou na pásku a studoval po celé dny. Už kvetou třešně,
lepkavé mladé listí se třpytí ve slunci, zlaté lilie rozvírají těžká poupata.
Po zahrádce chodí Anči s obtloustlou kamarádkou, obě se drží kolem pasu a
smějí se; teď sestrčily k sobě růžové čumáčky, něco si šeptají, zrudnou ve
smíchu a začnou se líbat.
Po létech zase cítí Prokop tělesné blaho. Živočišně se oddává slunci a mhouří
oči, aby naslouchal šumění svého těla. Vzdychne a sedá k práci; ale chce se mu
běhat, toulá se daleko po kraji a věnuje se náruživé radosti dýchat. Někdy
potká Anči v domě či v zahradě a pokouší se něco povídat; Anči se na něj dívá
po očku a neví co mluvit; ale ani Prokop to neví, a proto upadá do bručivého
tónu. Zkrátka je mu lépe nebo se aspoň cítí jistější, je-li sám.
Při studiu pozoroval, že mnoho zanedbal; věda byla už v mnohém dále a jinde,
leckdy se musel nově orientovat; a hlavně se bál vzpomínat na svou vlastní
práci, neboť tam, to cítil, se mu nejvíc potrhala souvislost. Pracoval jako
mezek nebo snil; snil o nových laboratorních metodách, ale zároveň ho lákal
jemný a odvážný kalkul teoretika; a vztekal se sám na sebe, když jeho hrubý
mozek nebyl s to rozštípnout teninký vlas problému. Byl si vědom, že jeho
laboratorní „destruktivní chemie“ otvírá nejpodivnější průhledy do teorie
hmoty; narážel na nečekané souvislosti, ale hned zas je rozšlapal svým příliš
těžkým uvažováním. Rozmrzen praštil vším, aby se ponořil do nějakého hloupého
románu; ale i tam ho pronásledovala laboratorní posedlost: místo slov četl
samé chemické symboly; byly to bláznivé vzorce plné prvků dosud neznámých, jež
ho znepokojovaly i ve snách.


XI.

Té noci se mu zdálo, že studuje veleučený článek v The Chemist. Zarazil se u
vzorce AnCi a nevěděl si s ním rady; hloubal, kousal se do kloubů a najednou
pochopil, že to znamená Anči. A vida, ona je vlastně tady a posmívá se mu s
pažema založenýma za hlavou; přistoupil k ní, chytil ji oběma rukama a začal
ji líbat a kousat do úst. Anči se divoce brání koleny a lokty; drží ji
brutálně a jednou rukou z ní trhá šaty v dlouhých pásech. Už cítí dlaněmi její
mladé maso; Anči sebou zběsile zmítá, vlasy padly jí přes tvář, teď, teď náhle
ochabuje a klesá; Prokop se vrhá k ní, ale nalézá pod rukama jen samé dlouhé
hadříky a fáče; trhá je, rve je, chce se z nich vyprostit, a probouzí se.
Hanbil se nesmírně za svůj sen; i ustrojil se potichu, sedl u okna a čekal na
svítání. Není hranice mezi nocí a dnem; jen nebe maličko pobledne, a vzduchem
proletí signál, jenž není ani světlo ani zvuk, ale poroučí přírodě: vzbuď se!
Tu tedy nastalo ráno ještě prostřed noci. Rozkřičeli se kohouti, zvířata v
stájích se pohnula. Nebe bledne do perleťova, rozzařuje se a lehce růžoví;
první červený pruh vyskočil na východě, „štilip štilip játiti piju piju já,“
štěkají a křičí ptáci, a první člověk jde volným krokem za svým povoláním.
Také učený člověk sedl k dílu. Dlouho kousal násadku, než se odhodlal napsat
první slova; neboť toto bude veliká věc, úhrn experimentování a přemýšlení
dvanácti let, práce opravdu vykoupená krví. Ovšem, to zde bude jen náčrt, či
spíš jistá fyzikální filozofie nebo báseň nebo vyznání víry. Bude to obraz
světa sklenutý z čísel a rovnic; avšak tyto cifry astronomického řádu měří
něco jiného než vznešenost oblohy: kalkulují vratkost a destrukci hmoty. Vše,
co jest, je tupá a vyčkávající třaskavina; ale jakékoliv budiž číslo její
netečnosti, je jenom mizivým zlomkem její brizance. Vše, co se děje, oběhy
hvězd a telurická práce, veškerá entropie, sám pilný a nenasytný život, to vše
jen na povrchu, nepatrně a neměřitelně ohlodává a váže tuto výbušnou sílu, jež
se jmenuje hmota. Vězte tedy, že pouto, jež ji váže, je jenom pavučina na
údech spícího titána; dejte mi sílu, aby jej pobodl, i setřese kůru země a
vrhne Jupitera na Saturna. A ty, lidstvo, jsi jenom vlaštovka, která si pracně
ulepila hnízdo pod krovem kosmické prachárny; cvrlikáš za slunce východu,
zatímco v sudech pod tebou mlčky duní strašlivý potenciál výbuchu…
Ty věci Prokop ovšem nepsal; byly mu jenom ztajenou melodií, jež okřídlovala
těžkopádné věty odborného výkladu. Pro něho bylo více fantazie v holém vzorci
a víc oslnivé krásy v číselném výrazu. A tak psal svou báseň ve značkách,
číslicích a děsné hantýrce učených slov.
K snídani nepřišel. Přišla tedy Anči a nesla mu mlíčko. Děkoval a přitom si
vzpomněl na svůj sen, a jaksi to nesvedl podívat se na ni. Koukal tvrdošíjně
do kouta; bůhví jak je to možno, že přesto viděl každý zlatý vlásek na jejích
holých pažích; nikdy si toho tak nevšiml.
Anči stála blizoučko. „Budete psát?“ ptala se neurčitě.
„Budu,“ bručel a myslel, co by tomu řekla, kdyby jí zničehonic položil hlavu
na prsa.
„Po celý den?“
„Po celý den.“ Asi by ucouvla náramně dotčena; ale má pevná, malá a široká
ňadra, o kterých snad ani neví. Ostatně, co s tím!
„Chtěl byste něco?“
„Ne, nic.“ Je to hloupé; chtěl by ji hryzat do paží či co; ženská nikdy neví,
jak člověka vyrušuje.
Anči pokrčila rameny trochu uraženě. „Taky dobře.“ A byla pryč.
Vstal a přecházel po pokoji; zlobil se na sebe i na ni, a hlavně se mu už
nechtělo psát. Sbíral myšlenky, ale naprosto se mu to nedařilo. Rozmrzel se a
otráven chodil od stěny ke stěně s pravidelností kyvadla. Hodinu, dvě hodiny.
Dole řinčí talíře, prostírá se k obědu. Sedl znovu k svým papírům a položil
hlavu do dlaní. Za chvíli tu byla služka a přinesla mu oběd.
Vrátil jídlo skoro netknuté a vrhl se rozmrzen na postel. Je zřejmo, že už ho
mají dost, že i on má toho všeho až po krk a že je načase odejet. Ano, hned
zítra. Dělal si nějaké plány pro příští práci, bylo mu neznámo proč stydno a
trapno a konečně z toho všeho usnul jako zabitý. Probudil se pozdě odpoledne s
duší zbahnělou a tělem zamořeným shnilou leností. Coural po pokoji, zíval a
bezmyšlenkovitě se mrzel. Setmělo se, a ani nerozsvítil.
Služka mu přinesla večeři. Nechal ji vystydnout a poslouchal, co se děje dole.
Vidličky cinkaly, doktor bručel a náramně brzo po večeři práskl dveřmi u svého
pokoje. Bylo ticho.
Jist, že už nikoho nepotká, sebral se Prokop a šel do zahrady. Byla vlažná a
jasná noc. Už kvetou šeříky a pustoryl, Bootes široce rozpíná na nebi svou
hvězdnou náruč, je ticho prohloubené dalekým psím štěkáním. O kamennou zídku v
zahradě se opírá něco světlého. Je to ovšem Anči.
„Je krásně, že?“ dostal ze sebe, aby vůbec něco řekl, a opřel se o zídku vedle
ní. Anči nic, jenom odvrací tvář a její ramena sebou nezvykle a neklidně
trhají.
„To je Bootes,“ bručel Prokop sdílně. „A nad ním… je Drak, a Cepheus, a tamto
je Kassiopeja, ty čtyři hvězdičky pohromadě. Ale to se musíte dívat výš.“
Anči se odvrací a něco roztírá kolem očí. „Tamta jasná,“ povídá Prokop váhavě,
„je Pollux, beta Geminorum. Nesmíte se na mne zlobit. Snad jsem se vám zdál
hrubý, že? Já jsem… něco mne trápilo, víte? Nesmíte na to dát.“
Anči zhluboka vzdychla. „A která je… tamta?“ ozvala se tichým, kolísavým
hláskem. „Ta nejjasnější dole.“
„To je Sírius, ve Velkém psu. Taky Alhabor mu říkají. A tamhle docela vlevo
Arcturus a Spica. Teď padala hvězda. Viděla jste?“
„Viděla. Proč jste se ráno na mne tak zlobil?“
„Nezlobil. Jsem snad… někdy… trochu hranatý; ale já jsem byl tvrdě živ, víte,
příliš tvrdě; pořád sám a… jako první hlídka. Nedovedu ani pořádně mluvit.
Chtěl jsem dnes… dnes napsat něco krásného… takovou vědeckou modlitbu, aby
tomu každý rozuměl; myslil jsem, že… že vám to přečtu; a vidíte, všechno ve
mně vyschlo, člověk už se stydí… rozehřát se, jako by to byla slabost. Nebo
vůbec něco říci ze sebe. Takový okoralý, víte? Už hodně šedivím.“
„Vždyť vám to sluší,“ vydechla Anči.
Prokopa překvapila tato stránka věci. „Nu víte,“ řekl zmateně, „příjemné to
není. Už by byl čas… už by byl čas svážet svou úrodu domů. Co by jiný udělal z
toho, co já vím! A já nemám nic, nic, nic z toho všeho. Jsem jenom… ,berühmt‘
a ,célčbre‘ a ,highly esteemed‘; ani o tom… u nás… nikdo neví. Já myslím,
víte, že mé teorie jsou dost špatné; já nemám hlavu na teoretika. Ale co jsem
našel, není bez ceny. Mé exotermické třaskaviny… diagramy… a exploze atomů… to
má nějakou cenu. A publikoval jsem sotva desetinu toho, co vím. Co by z toho
jiný udělal! Já už… ani nerozumím jejich teoriím; jsou tak subtilní, tak
duchaplné… a mne to jen mate. Jsem kuchyňský duch. Dejte mně k nosu nějakou
látku, a já zrovna čichám, co se s ní dá dělat. Ale pochopit, co z toho plyne…
teoreticky a filozoficky…, to neumím. Já znám… jen fakta; já je dělám; jsou to
má fakta, rozumíte? A přece… já… já za nimi cítím nějakou pravdu; ohromnou
obecnou pravdu… která všechno převrátí… až vybuchne. Ale ta velká pravda… je
za fakty a ne za slovy. A proto, proto musíš za fakty! až ti to třeba obě ruce
utrhne…“
Anči, opřena o zídku, sotva dýchala. Nikdy dosud se ten zamračený patron tolik
nerozmluvil – a hlavně nikdy nemluvil o sobě. Zápasil těžce se slovem; zmítala
jím ohromná pýcha, ale také plachost a zmučenost; a kdyby mluvil třeba v
integrálách, chápala Anči, že se před ní děje něco naprosto niterného a lidsky
zjitřeného.
„Ale to nejhorší, to nejhorší,“ bručel Prokop. „Někdy… a tady zvlášť… i to, i
to se mně zdá hloupé… a k ničemu. I ta konečná pravda… vůbec všecko. Nikdy
dřív mně to tak nepřišlo. Nač, a k čemu… Snad je rozumnější poddat se… prostě
poddat se tomu, tomu všemu – (Nyní ukázal rukou cosi kolem dokola.) Prostě
životu. Člověk nemá být šťastný; to ho změkčuje, víte? Pak se mu zdá všechno
ostatní zbytečné, malé… a nesmyslné. Nejvíc… nejvíc udělá člověk ze
zoufalství. Ze stesku, ze samoty, z ohlušování. Protože mu nic nestačí. Já
jsem pracoval jako blázen. Ale tady, tady jsem začal být šťastný. Tady jsem
poznal, že je snad… něco lepšího než myslet. Tady člověk jenom žije… a vidí,
že je to něco ohromného… jenom žít. Jako váš Honzík, jako kočka, jako slepice.
Každé zvíře to umí… a mně to připadá tak ohromné, jako bych dosud nežil. A
tak… tak jsem podruhé ztratil dvanáct let.“
Jeho potlučená, bůhvíkolikrát sešívaná pravice se chvěla na zídce. Anči mlčí,
i potmě je vidět její dlouhé řasy; opírá se lokty a hrudí o zděný plot a mžiká
k hvězdičkám. Tu zašelestilo něco v křoví, a Anči se zděsila; až ji to mocí
vrhlo k Prokopovu rameni. „Co je to?“
„Nic, nejspíš kuna; jde asi do dvora, na kuřata.“
Anči znehybněla. Její mladé prsy se nyní pružně, plně opírají o Prokopovu
pravici, – snad, jistě o tom sama neví, ale Prokop to ví víc než cokoliv na
světě; bojí se hrozně pohnout rukou, neboť, předně, by si Anči myslela, že ji
tam položil schválně, a za druhé by vůbec změnila polohu. Zvláštní však je, že
tato okolnost vylučuje, aby dále mluvil o sobě a o ztraceném životě. „Nikdy,“
koktá zmateně, „nikdy jsem nebyl tak rád… tak šťasten jako tady. Váš tatík je
nejlepší člověk na světě, a vy… vy jste tak mladá…“
„Já jsem myslela, že se vám zdám… příliš hloupá,“ povídá Anči tiše a šťastně.
„Nikdy jste se mnou takhle nemluvil.“
„Pravda, nikdy dosud,“ zabručel Prokop. Oba se odmlčeli. Cítil na ruce lehké
oddechování jejích ňader; mrazilo ho a tajil dech, i ona, zdá se, tají dech v
tichém trnutí, ani nemrká a široce hledí nikam. Oh, pohladit a stisknout! Oh,
závrati, prvý dotyku, lichotko bezděčná a horoucí! Zda tě kdy potkalo
dobrodružství opojnější než tato nevědomá a oddaná důvěrnost? Skloněné poupě,
tělo bázlivé a jemné! kdybys tušilo mučivou něhu té tvrdé chlapské ruky, jež
tě bez hnutí hladí a svírá! Kdybys – kdyby – kdybych teď učinil… a stiskl…
Anči se vztyčila nejpřirozenějším pohybem. Ach, děvče, tys tedy opravdu o
ničem nevědělo! „Dobrou noc!“ povídá Anči tiše, a její tvář je bledá a
nejasná. „Dobrou noc,“ praví trochu sevřeně a podává mu ruku; podává ji levě a
chabě, je jako polámaná a dívá se široce nějak jinam. Není-liž pak to, jako by
chtěla ještě prodlít? Ne, jde už, váhá; ne, stojí a trhá na kousíčky nějaký
lístek. Co ještě říci? Dobrou noc, Anči, a spěte lépe než já.
Neboť zajisté nelze teď jít spat. Prokop se vrhá na lavičku a položí hlavu do
dlaní. Nic, nic se neudálo… tak dalece; bylo by hanebné hnedle myslet na
bůhvíco. Anči je čistá a nevědomá jako telátko, a teď už dost o tom; nejsem
přece chlapec. Tu se rozsvítilo v prvním patře okno. Je to Ančina ložnice.
Prokopovi bouchá srdce. Ví, že je to hanebnost, tajně se tam dívat; jistě, to
by jako host dělat neměl. Pokouší se dokonce zakašlat (aby ho slyšela), ale
jaksi to selhalo; i sedí jako socha a nemůže odvrátit očí od zlatého okna.
Anči tam přechází, shýbá se, něco dlouze a široce robí; aha, rozestýlá si
postýlku. Teď stojí u okna, dívá se do tmy a zakládá ruce za hlavou: zrovna
tak ji viděl ve snu. Teď, teď by bylo radno se ozvat; proč to neudělal? Už je
na to pozdě; Anči se odvrací, přechází, je ta tam; ba ne, to sedí zády k oknu
a zřejmě se zouvá hrozně pomalu a zamyšleně; nikdy se nesní líp než se
střevícem v ruce. Aspoň teď by bylo načase zmizet; ale místo toho vylezl na
lavičku, aby líp viděl. Anči se vrací, už nemá na sobě živůtek; zvedá nahé
paže a vyndává si z účesu vlásničky. Nyní hodila hlavou, a celá hříva se jí
rozlévá po ramenou; děvče jí potřese, hurtem si přehodí celou tu úrodu vlasů
přes čelo a teď ji zpracovává kartáčem a hřebenem, až má hlavu jako cibulku;
je to patrně velmi směšné, neboť Prokop, hanebník, přímo září.
Anči, panenka bílá, stojí se skloněnou hlavou a splétá si vlasy ve dva copy;
má víčka sklopena a něco si šeptá, zasměje se, zastydí se, až jí to ramena
zvedá; pásek košile, pozor, sklouzne. Anči hluboce přemýšlí a hladí si bílé
ramínko v nějakém rozkošnictví, zachvěje se chladem, pásek se smeká už
povážlivě, a světlo zhaslo.
Nikdy jsem neviděl nic bělejšího, nic pěknějšího a bělejšího než toto
osvětlené okno.


XII.

Hned ráno ji zastihl, jak drhne mydlinkami Honzíka v neckách; psisko zoufale
vytřepávalo vodu, ale Anči se nedala, držela ho za čupřiny a náruživě mydlila,
postříkaná, zmáčená na břiše a usmátá. „Pozor,“ křičela z dálky, „postříká
vás!“ Vypadala jako mladá nadšená maminka; oj bože, jak je vše prosté a jasné
na tomto slunném světě!
Ani Prokop nevydržel zahálet. Vzpomněl si, že nefunguje zvonek, a jal se
spravovat baterii. Zrovna oškrabával zinek, když se k němu tiše blížila ona;
měla rukávy po loket vyhrnuté a mokré ruce, neboť se pere. „Nevybuchne to?“
ptá se starostlivě. Prokop se musel usmát; i ona se zasmála a stříkla po něm
mydlinkami; ale hned mu šla s vážnou tváří utřít loktem bublinku mýdla na
vlasech. Hle, včera by se toho nebyla odvážila.
K polednímu vleče s Nandou koš prádla na zahradu; bude se bílit. Prokop s
povděkem sklapl knihu; nenechá ji přece tahat se s těžkou kropicí konví.
Zmocnil se konve a kropí prádlo; hustá prška přeradostně a horlivě bubnuje na
řásné ubrusy a na bělostné rozložité povlaky a do široce rozevřených náručí
mužských košil, šumí, crčí a slévá se ve fjordy a jezírka. Prokop se žene
zkropit i bílé zvonky sukének a jiné zajímavé věci, ale Anči mu vyrve konev a
zalévá sama. Zatím si Prokop sedl do trávy, dýchá s rozkoší vůni vlhkosti a
pozoruje Ančiny činné a krásné ruce. Soi de theoi tosa doien, vzpomněl si
zbožně. Sebas m’echei eisoroónta. Já s úžasem na tebe hledím.
Anči usedá k němu do trávy. „Nač jste to myslel?“ Mhouří oči oslněním a
radostí, zardělá a kdovíproč tak šťastná. Rve plnou hrstí svěží trávu a chtěla
by mu ji z bujnosti hodit do vlasů; ale bůhví, i teď ji tísní jakýsi uctivý
ostych před tím ochočeným hrdinou. „Měl jste někdy někoho rád?“ ptá se
zčistajasna a honem se dívá jinam.
Prokop se směje. „Měl. Vždyť i vy jste už měla někoho ráda.“
„To jsem byla ještě hloupá,“ vyhrkne Anči a proti své vůli se červená.
„Študent?“
Anči jen kývne a kouše nějakou travinu. „To nic nebylo,“ povídá pak rychle. „A
vy?“
„Jednou jsem potkal děvče, které mělo takové řasy jako vy. Možná že vám byla
podobná. Prodávala rukavice či co.“
„A co dál?“
„Nic dál. Když jsem tam šel podruhé koupit rukavice, už tam nebyla.“
„A… líbila se vám?“
„Líbila.“
„A… nikdy jste ji…“
„Nikdy. Teď mně dělá rukavice… bandažista.“
Anči soustřeďuje svou pozornost na zem. „Proč… vždycky přede mnou schováváte
ruce?“
„Protože… protože je mám tak rozbité,“ děl Prokop a chudák se začervenal.
„To je zrovna tak krásné,“ šeptá Anči s očima sklopenýma.
„K obědúúú, k obědúúú,“ vyvolává Nanda před domem. „Bože, už,“ vzdychne Anči a
velmi nerada se zvedá.
Po obědě se starý doktor jen tak trochu položil, jen docela málo. „Víte,“
omlouval se, „já jsem se ráno nadřel jako pes.“ A hned začal pravidelně a
pilně chrupat. Zasmáli se na sebe očima a po špičkách vyšli; a i v zahradě
mluvili potichu, jako by ctili jeho sytý spánek.
Prokop musel povídat o svém životě. Kde se narodil a kde rostl, že byl až v
Americe, co bídy poznal, co kdy dělal. Dělalo mu dobře zopakovat si celý ten
život; neboť, kupodivu, byl klikatější a divnější, než by sám myslel; a ještě
o mnohém pomlčel, zejména, nu, zejména o jistých citových záležitostech, neboť
předně to nemá takový význam, a za druhé, jak známo, každý mužský má o čem
mlčet. Anči byla tichá jako pěna; připadalo jí jaksi směšné a zvláštní, že
Prokop byl také dítětem a chlapcem a vůbec něčím jiným než bručivým a divným
člověkem, vedle něhož se cítí taková nesvá a maličká. Nyní by se už nebála na
něho i sáhnout, zavázat mu kravatu, pročísnout vlasy nebo vůbec. A poprvé
viděla teď jeho tlustý nos, jeho drsná ústa a přísné, mračné, krvavě protkané
oči; připadalo jí to vše nesmírně divné.
A nyní byla řada na ní, aby povídala o svém životě. Už otevřela ústa a
nabírala dechu, ale dala se do smíchu. Uznejte, co se může říci o tak
nepopsaném životě, a dokonce někomu, kdo už jednou byl dvanáct hodin zasypán,
kdo byl ve válce, v Americe a kdovíkde ještě? „Já nic nevím,“ řekla upřímně.
Nuže, řekněte, není takové „nic stejně cenné jako mužovy zkušenosti?
Je pozdě odpoledne, když spolu putují vyhřátou polní stezkou. Prokop mlčí a
Anči poslouchá. Anči hladí rukou ostnaté vrcholky klasů. Anči se ho dotýká
ramenem, zpomaluje krok, vázne; pak zase zrychlí chůzi, jde dva kroky před ním
a rve klasy v jakési potřebě ničit. Tato slunečná samota je posléze tíží a
znervózňuje; neměli jsme sem jít, myslí si oba potají, a v tísnivém rozladění
soukají ze sebe plytký, potrhaný hovor. Konečně tady je cíl, kaplička mezi
dvěma starými lípami; je pozdní hodina, kdy pasáci začínají zpívat. Tu je
sedátko poutníků; usedli a jaksi ještě víc potichli. Nějaká žena klečela u
kapličky a modlila se, jistěže za svou rodinu. Sotva odešla, zvedla se Anči a
klekla na její místo. Bylo v tom něco nekonečně a samozřejmě ženského; Prokop
se cítil chlapcem vedle zralé prostoty tohoto pravěkého a posvátného gesta.
Anči konečně vstala, zvážnělá jaksi a vyspělá, o čemsi rozhodnutá, s čímsi
smířená; jako by něco poznala, jako by něco v sobě nesla, přetížená,
zamyšlená, bůhvíčím tak změněná; jen slabikami odpovídala sladkým a potemnělým
hlasem, když se loudali domů cestičkou soumraku.
Nemluvila při večeři a nemluvil ani Prokop; mysleli asi na to, kdy starý pán
si půjde přečíst noviny. Starý pán bručel a zkoumal je přes brejličky;
holenku, něco se mu tady netento, nezdálo jaksi v pořádku. Už se to trapně
táhlo, když se ozval zvonek a člověk odněkud ze Sedmidolí nebo ze Lhoty prosil
doktora k porodu. Starý doktor byl pramálo potěšen, zapomněl dokonce hubovat.
Ještě s porodním tlumokem zaváhal ve dveřích a kázal suše: „Jdi spat, Anči.“
Beze slova se zvedla a sklízela se stolu. Byla dlouho, velmi dlouho někde v
kuchyni. Prokop nervózně kouřil a už chtěl odejít. Tu se vrátila, bledá, jako
by ji mrazilo, a řekla s hrdinným přemáháním: „Nechcete si zahrát biliár?“ To
znamenalo: se zahradou dnes nic nebude.
Nu, byla to prašpatná partie; zejména Anči byla zrovna toporná, šťouchala
naslepo, zapomínala hrát a stěží odpovídala. A když jednou zahodila
nejvyloženějšího sedáka, ukazoval jí Prokop, jak to měla sehrát: pravá faleš,
vzít trochu dole, a je to; při tom – jen aby jí vedl ruku – položil svou ruku
na její. Tu Anči prudce, temně mu vzhlédla do tváře, hodila tágo na zem a
utekla.
Nuže, co dělat? Prokop pobíhal po salóně, kouřil a mrzel se. Eh, divné děvče;
ale proč to tak mate mne sama? Její hloupá pusa, jasné blizoučké oči, líčko
hladké a horoucí, nu, člověk není konečně ze dřeva. Což by bylo takovým
hříchem pohladit líčko, políbit, pohladit, ach, růžové líce, a požehnat vlasy,
vlasy, přejemné vlásky nad mladou šíjí (člověk není ze dřeva); políbit,
pohladit, vzít do rukou, pocelovat zbožně a opatrně? Hlouposti, mrzel se
Prokop; jsem starý osel; což bych se nestyděl – takové dítě, které na to ani
nemyslí, ani nemyslí – Dobrá; toto pokušení vyřídil Prokop sám se sebou, ale
tak rychle to nešlo; mohli byste jej vidět, jak stojí před zrcadlem se rty do
krve rozkousanými a mračně, hořce vyzývá a měří svá léta.
Jdi spat, starý mládenče, jdi; právě sis ušetřil ostudu, až by se ti mladá,
hloupá holčička vysmála; i tenhle výsledek stojí za to. Jakžtakž odhodlán
stoupal Prokop nahoru do své ložnice; jen ho tížilo, že musí tadyhle projít
podle Ančina pokojíčku. Šel po špičkách: snad už spí, dítě. A najednou stanul
se srdcem splašeně tlukoucím. Ty dveře… Ančiny… nejsou dovřeny. Nejsou vůbec
zavřeny a za nimi tma. Co je to? A tu slyšel uvnitř cosi jako zakvílení.
Něco ho chtělo vrhnout tam, do těch dveří; ale něco silnějšího jej tryskem
srazilo se schodů dolů a ven do zahrady. Stál v temném houští a tiskl ruku k
srdci, jež bouchalo jako na poplach. Kristepane, že jsem k ní nešel! Anči
jistě. klečí – polosvlečena – a pláče do peřinky, proč? to nevím; ale kdybych
byl vešel – nuže, co by se stalo? Nic; klekl bych vedle ní a prosil, aby
neplakala; pohladil, pohladil bych lehké vlasy, vlásky už rozpuštěné – Ó bože,
proč nechala otevřeno?
Ejhle, světlý stín vyklouzl z domu a míří do zahrady. Je to Anči, není
svlečena ani nemá vlasy rozpuštěné, ale tiskne ruce k skráním, neboť na
palčivém čele ruce chladí; a štká ještě posledním dozvukem pláče. Jde podle
Prokopa, jako by ho neviděla, ale dělá mu místo po svém pravém boku; neslyší,
nevidí, ale nebrání se, když ji bere pod paží a vede k lavičce. Prokop zrovna
sbírá nějaká slova chlácholení (u všech všudy, o čem vlastně?), když náhle,
bác, má na rameni její hlavu, ještě jednou to křečovitě zapláče, a prostřed
vzlyků a smrkání to odpovídá, že „to nic není“; Prokop ji obejme rukou, jako
by jí byl rodným strýčkem, a nevěda si jinak rady bručí cosi, že je hodná a
strašně milá; načež vzlyky roztály v dlouhé vzdechy (cítil kdesi v podpaží
jejich horoucí vlhkost) a bylo dobře. Ó noci, nebešťanko, ty ulevíš sevřené
hrudi a rozvážeš těžký jazyk; povzneseš, požehnáš, okřídlíš tiše tlukoucí
srdce, srdce teskné a zamlklé; žíznivým dáváš pít ze své nekonečnosti. V
kterémsi mizivém bodě prostoru, někde mezi Polárkou a Jižním křížem, Centaurem
a Lyrou se děje dojatá věc; nějaký muž se zničehonic cítí jediným ochráncem a
tátou tady té mokré tvářičky, hladí ji po temeni a povídá – co vlastně? Že je
tak šťasten, tak šťasten, že má tak rád, hrozně rád to štkající a
posmrkávající na svém rameni, že nikdy odtud neodejde a kdesi cosi.
„Já nevím, co mne to napadlo,“ vzlyká a vzdychá Anči. „Já… já jsem tak chtěla
s vámi ještě… mluvit…“
„A proč jste plakala?“ bručel Prokop.
„Protože jste tak dlouho nešel,“ zní překvapující odpověď.
V Prokopovi něco slábne, vůle či co. „Vy… vy mne… máte ráda?“ vysouká ze sebe,
a hlas mu mutuje jako čtrnáctiletému. Hlava zarytá v jeho podpaží prudce a bez
výhrady kývá.
„Snad jsem… měl za vámi přijít,“ šeptá Prokop zdrcen. Hlava rozhodně vrtí, že
ne. „Tady… je mi líp,“ vydechne Anči po chvíli. „Tady je… tak krásně!“ Nikdo
snad nepochopí, co je tak krásného na drsném mužském kabátě, čpícím tabákem a
tělesností; ale Anči do něho zarývá tvář a za nic na světě by ji neobrátila k
hvězdičkám: tak je šťastna v tomto tmavém a kořenném úkrytu. Její vlasy
šimrají Prokopa pod nosem a voní přepěknou vůničkou. Prokop jí hladí schýlená
ramena, hladí její mladičkou šíji a hruď, a nalézá jenom chvějící se oddanost;
tu zapomínaje na vše, prudký a brutální popadne její hlavu a chce ji políbit
na mokré rty. A hle, Anči se divoce brání, přímo tuhne hrůzou a jektá „ne ne
ne“; a už zas se zavrtala tváří do jeho kabátu a je cítit, jak v ní buchá
poplašené srdce. A Prokop náhle pochopí, že měla být políbena poprvé.
Tu se zastyděl za sebe, zněžněl nesmírně a neodvážil se již ničeho více než ji
hladit po vlasech: to se smí, to se smí; bože, vždyť je to docela ještě dítě a
úplný pitomec! A nyní již ani slova, ani slovíčka, jež by se jen dechem dotklo
neslýchaného dětství této bílé, veliké jalovičky; ani myšlenky, která by
chtěla hrubě vysvětlit zmatené pohnutky tohoto večera! Nevěděl věru, co
povídá; mělo to medvědí melodii a pražádnou syntaxi; týkalo se to střídavě
hvězd, lásky, boha, krásné noci a kterési opery, na jejíž jméno a děj si
Prokop živou mocí nemohl vzpomenout, ale jejíž smyčce a hlasy v něm opojně
zvučely. Chvílemi se mu zdálo, že Anči usnula; i umlkal, až zase pocítil na
rameni blažený dech ospalé pozornosti.
Posléze se Anči vzpřímila, složila ruce v klín a zamyslela se. „Já ani nevím,
já ani nevím,“ povídá sladce, „mně se to ani nezdá možné.“
Po nebi světlou proužkou padá hvězda. Pustoryl voní, tady spí zavřené koule
pivoněk, jakýsi božský dech šelestí v korunách stromů. „Já bych tu tak chtěla
zůstat,“ šeptá Anči.
Ještě jednou bylo Prokopovi svésti němý boj s pokušením. „Dobrou noc, Anči,“
dostal ze sebe. „Kdyby… kdyby se vrátil váš tati…“
Anči poslušně vstala. „Dobrou noc,“ řekla a váhala; tak stáli proti sobě a
nevěděli, co počít nebo skončit. Anči byla bledá, rozčileně mžikala a
vypadala, jako by se chtěla odhodlat k nějakému hrdinství; ale když Prokop –
už nadobro ztráceje hlavu – vztáhl ruku po jejím lokti, uhnula zbaběle a dala
se na ústup. Tak šli zahradní stezičkou dobře na metr od sebe; ale když došli
tam, co je ten nejčernější stín, patrně ztratili směr či co, neboť Prokop
narazil zuby na nějaké čelo, políbil chvatně studený nos a našel svými ústy
rty zoufale semknuté; tu je rozryl hrubou přesilou, lámaje děví šíji vypáčil
jektající zuby a ukrutně líbal horoucí vláhu otevřených, sténajících úst. Pak
už se mu vydrala z rukou, postavila se u zahradních vrátek a vzlykala. Tu ji
běží Prokop těšit, hladí ji, rozsévá hubičky do vlasů a na ucho, na šíj a na
záda, ale nepomáhá to; prosí, obrací k sobě mokré líčko, mokré oči, mokrou a
štkající pusu, má ústa plná slanosti slz, celuje a hladí, a náhle vidí, že ona
se už ničemu nebrání, že se vzdala na milost a nemilost a snad pláče nad svou
hroznou porážkou. Nuže, všechno mužské rytířství rázem procitá v Prokopovi;
pouští z náručí tu hromádku neštěstí a nesmírně dojat líbá jenom zoufalé prsty
smáčené slzami a třesoucí se. Tak, tak je to lépe; a tu zas ona složí tvář na
jeho hrubou pracku a celuje ji vlhkou, palčivou pusou a horkým dechem a
tlukotem zrosených řas, a nedá si ji vzít. A tu i on mžiká očima a tají dech,
aby nevzdychl mukou něhy.
Anči zvedla hlavu. „Dobrou noc,“ povídá tiše a nastaví zcela prostě rty.
Prokop se k nim skloní, vdechne na ně polibek, jak jemný jen umí, a už se ani
neodváží ji doprovodit dál; stojí a trne, a pak se klidí až na druhý konec
zahrady, kam nepronikne ani paprsek z jejího okna: stojí a vypadá, jako by se
modlil. Nikoliv, není to modlitba; je to jen nejkrásnější noc života.


XIII.

Když svítalo, nemohl už vydržet doma: umínil si, že poběží natrhat květin; pak
je položí na práh Ančiny ložnice, a až ona vyskočí… Okřídlen radostí vykradl
se Prokop z domu málem už ve čtyři ráno. Lidi, je to krása; každý květ jiskří
jako oči (ona má mírné, veliké oči kravičky) (ona má tak dlouhé řasy) (teď
spí, má víčka oblá a něžná jako vajíčka holubí) (bože, znát její sny) (má-li
ruce složeny na prsou, zvedají se dechem; ale má-li je pod hlavou, tu jistě se
jí shrnul rukáv a je vidět loket, kolečko drsné a růžové) (onehdy říkala, že
spí dosud v železné dětské postýlce) (říkala, že v říjnu jí bude už
devatenáct) (má na krku mateřské znamínko) (jak jen je možno, že mne má ráda,
to je tak divné), vskutku, nic se nevyrovná kráse letního jitra, ale Prokop se
dívá do země, usmívá se, pokud to vůbec dovede, a putuje samými závorkami až k
řece. Tam objeví – ale u druhého břehu – poupata leknínů; tu zhrdaje vším
nebezpečím se svlékne, vrhne se do hustého slizu zátoky, pořeže si nohy o
nějakou zákeřnou ostřici a vrací se s náručí leknínů. Leknín je květina
poetická, ale pouští ošklivou vodu z tučných stvolů; i běží Prokop s poetickou
kořistí domů a přemýšlí, z čeho by udělal na svou kytku pořádnou manžetu.
Vida, na lavičce před domem zapomněl doktor svou včerejší Političku. Prokop ji
chutě trhá, zhola přehlížeje jakousi balkánskou mobilizaci, i to, že se houpe
nějaké ministerstvo a že někdo v černém rámečku zemřel, oplakáván ovšem celým
národem, a balí do toho mokré řapíky. Když pak se chtěl s pýchou podívat na
své dílo, hrklo v něm hrozně. Na manžetě z novin našel totiž jedno slovo. Bylo
to KRAKATIT.
Chvíli na to strnule koukal nevěře prostě svým očím. Pak rozbalil se zimničným
spěchem noviny, rozsypal celou nádheru leknínů po zemi a našel konečně tento
inzerát: „KRAKATIT! Ing. P. ať udá svou adresu. Carson, hl. p.“ Nic víc.
Prokop si vytíral oči a četl znova: „Ing. P. ať udá svou adresu. Carson.“ Co u
všech všudy… Kdo je to, ten Carson? A jak ví, hrome, jak může vědět…
Popadesáté četl Prokop záhadný inzerát: „KRAKATIT! Ing. P. ať udá svou
adresu.“ A pak ještě „Carson, hl. p.“ Víc už se z toho vyčíst nedalo.
Prokop seděl jako praštěný palicí. Proč, proč jen jsem vzal ty proklaté noviny
do rukou, mihlo se mu zoufale hlavou. Jakže to tam je? „KRAKATIT! Ing. P. ať
udá svou adresu.“ Ing. P., to znamená Prokop; a Krakatit, to je právě to
zatracené místo, to zamžené místo tadyhle v mozku, ten těžký nádor, to, nač si
netroufal myslet, s čím chodil tluka hlavou do zdí, to, co už nemělo jména, –
jakže to tu stojí? „KRAKATIT!“ Prokop vytřeštil oči vnitřním nárazem. Najednou
viděl… tu jistou olovnatou sůl, a rázem se mu rozvinul zmatený film paměti:
předlouhý, zuřivý zápas v laboratoři s tou těžkou, tupou, netečnou látkou;
slepé a sviňské pokusy, když selhávalo vše, žíravý ohmat, když vztekem ji
drobil a drtil v prstech, leptavá chuť na jazyku a čpavý dým, únava, jíž
usínal na židli, stud, zarytost a najednou – snad ve snu či jak – poslední
nápad, pokus paradoxní a zázračně jednoduchý, fyzikální trik, jehož doposud
neužil. Viděl teninké bílé jehličky, jež konečně smetl do porcelánové krabice,
přesvědčen, že to zítra pěkně bouchne, až to zapálí v pískové jámě tam v
polích, kde byla jeho velmi protizákonná pokusná střelnice. Viděl svou
laboratorní lenošku, z níž čouhá koudel a dráty; tam tehdy se stočil jako
unavený pes a patrně usnul, neboť byla úplná tma, když za strašlivé exploze a
řinkotu skla se skácel i s lenoškou na zem. Pak přišla ta prudká bolest na
pravé ruce, neboť něco mu ji rozseklo; a potom – potom –
Prokop vraštil čelo bolestně prudkým rozpomínáním. Pravda, tady je přes ruku
ta jizva. A potom jsem chtěl rozsvítit, ale žárovky byly prasklé. Pak jsem
hmatal potmě, co se to stalo; na stole plno střepů, a tuhle, kde jsem
pracoval, je zinkový plech pultu roztrhán, zkroucen a seškvařen a dubová
tabule rozštípnuta, jako by do ní sjel blesk. A pak jsem nahmátl tu
porcelánovou krabici, a byla celá, a tehdy teprve jsem se zděsil. Tohle, ano,
tohle tedy byl Krakatit. A potom –
Prokop už nevydržel sedět; překročil rozsypané lekníny a běhal po zahradě
hryže si rozčilením prsty. Potom jsem někam běžel, přes pole, přes oranice,
několikrát jsem se svalil, bože, kde to vlastně bylo? Tady byla souvislost
vzpomínek rozhodně porušena; nepochybná je jenom hrozná bolest pod čelními
kostmi a jakási okolnost s policií, potom jsem mluvil s Jirkou Tomšem a šli
jsme k němu, ne, jel jsem tam drožkou; byl jsem nemocen a on mne ošetřoval.
Jirka je hodný. Proboha, jak to bylo dál? Jirka Tomeš řekl, že jede sem, k
tátovi, ale nejel; hleďme, je to divné; zatím já jsem spal či co –
Tu krátce, jemně zazněl zvonek; šel jsem otevřít, a na prahu stála dívka s
tváří zastřenou závojem.
Prokop zasténal a zakryl si obličej rukama. Ani nevěděl, že sedí na lavičce,
kde této noci mu bylo hladit a konejšit někoho jiného. „Bydlí tady pan Tomeš?“
ptala se udýchaně; asi běžela, kožišinku měla zrosenou deštěm, a náhle, náhle
zvedla oči –
Prokop málem zavyl útrapou. Viděl ji, jako by to včera bylo: ruce, maličké
ruce v těsných rukavičkách, rosička dechu na hustém závoji, pohled čistý a
plný hoře; krásná, smutná a statečná. „Vy ho zachráníte, že?“ Dívá se na něho
zblízka vážnýma, matoucíma očima a mačká nějaký balíček, nějakou silnou obálku
s pečetěmi, tiskne ji k prsoum rozčilenýma rukama a přemáhá se všemožně –
Prokopa jako by udeřilo do tváře. Kam jsem dal ten balíček? Ať kdokoliv je ta
dívka: slíbil jsem, že jej odevzdám Tomšovi. Ve své nemoci… jsem na všecko
zapomněl; nebo jsem… spíš… na to nechtěl myslet. Ale teď – Musí se teď nalézt,
toť jasno.
Skokem vyběhl do svého pokoje a rozhazoval zásuvky. Není, není, není tu nikde.
Podvacáté přehazoval svých pět švestek, list po listu a kus po kuse; pak usedl
prostřed toho strašného nepořádku jako nad zříceninami Jeruzaléma a ždímal si
čelo. Buď to vzal doktor nebo Anči nebo řehtavá Nanda; jinak to už není možno.
Když toto nezvratně a detektivně zjistil, pocítil jakousi nevolnost nebo
zmatek a jako ve snu šel ke kamnům, sáhl hluboko dovnitř a vyňal… hledaný
balíček. Přitom se mu nejasně zdálo, že jej tam kdysi uložil sám, kdysi, když
ještě nebyl… docela zdráv; nějak se upomínal, že v onom stavu mrákot a
blouznění jej pořád musel mít v posteli a zuřil, když mu jej brali, a že se ho
přitom hrozně bál, neboť pojil se k němu mučivý neklid a stesk. Patrně jej tam
se lstivostí blázna ukryl sám před sebou, aby měl od něho pokoj. Čert se
ostatně vyznej v tajemstvích podvědomí; teď je to tady, ta silná převázaná
obálka s pěti pečetěmi, a na ní napsáno „Pro pana Jiřího Tomše“. Snažil se
vyčíst něco bližšího z toho zralého a pronikavého písma; ale místo toho viděl
zastřenou dívku, jak ždímá obálku v třesoucích se prstech; teď, teď zase zvedá
oči… Přivoněl žíznivě k balíčku: voněl slabounce a vzdáleně.
Položil jej na stůl a kroužil dokola. Hrozně by chtěl vědět, co je tam uvnitř,
pod pěti pečetěmi; zajisté je to těžké tajemství, nějaký poměr osudný a
palčivý. Říkala sice, že… že to činí pro někoho jiného; ale byla tak rozčilena
– Nicméně že by ona, ona mohla milovat Tomše: toť neuvěřitelno. Tomeš je
darebák, zjišťoval s temným vztekem; vždycky měl u ženských štěstí, ten cynik.
Dobrá, najdu ho a odevzdám mu tu zásilku lásky; a pak ať už je konec –
Najednou se mu rozbřesklo v hlavě: oč že je nějaká souvislost mezi Tomšem a
tím, jakpak se jmenuje, tím zatraceným Carsonem! Nikdo přece nevěděl a neví o
Krakatitu; jen Tomeš Jirka to asi bůhvíjak vytento, vyšpehoval – Nový obrázek
se sám sebou vsunul do zmateného filmu paměti: kterak tehdy on, Prokop, něco
brebentil v horečce (to je asi byt Tomšův), a on, Jirka, se nad ním sklání a
něco si zaznamenává v notesu. Určitě a svatosvatě to byl můj vzorec! vyžvanil
jsem to, vylákal to ze mne, ukradl mi to a prodal to asi tomu Carsonovi!
Prokop ustrnul nad takovou špatností. Ježíši, a tomu člověku padlo to děvče do
rukou! Je-li co na světě jasno, tož je to: že je nutno ji zachránit, stůj co
stůj!
Dobrá, nejprve musím nalézt Tomše, zloděje; dám mu tady ten zapečetěný balíček
a mimoto mu vyrazím zuby. Dále, mám ho jednoduše v hrsti: musí mně říci jméno
a pobyt toho děvčete a zavázat se – ne; žádné sliby od takového ničemy. Ale
půjdu k ní a řeknu jí vše. A potom zmizím navždy z jejích očí.
Uspokojen tímto rytířským řešením stanul Prokop nad nešťastnou obálkou. Ach,
vědět jen to, jen to jediné, zda byla milenkou Tomšovou! Zase ji viděl, jak
stojí, sličná a silná; ani pohledem, ani mžiknutím tehdy nezavadila o hříšné
lože Tomšovo. Což bylo by možno tak lhát očima, tak lhát takovýma očima –
Tu syknuv utrpením zlomil pečetě, přerval provázek a roztrhl obálku. Byly tam
bankovky a dopis.


XIV.

Zatím už doktor Tomeš sedí u snídaně funě a bruče po těžkém porodu; přitom
vrhá na Anči pohledy zkoumavé a nespokojené. Anči sedí jako zařezaná, nejí,
nepije, nevěří prostě svým očím, že se Prokop ještě neukázal; nějak se jí
třesou rty, patrně užuž přijdou slzy. Tu vejde Prokop jaksi zbytečně rázně, je
bledý a nemůže si ani sednout, jak má naspěch; jen taktak že pozdraví,
přeběhne Anči očima, jako by ji ani neznal, a hned se ptá s popudlivou
netrpělivostí: „Kde je teď váš Jirka?“
Doktor se užasle otočil: „Cože?“
„Kde je teď váš syn,“ opakuje Prokop a sžehuje ho umíněnýma očima.
„Copak já vím?“ zavrčí doktor. „Já o něm nechci vědět.“
„Je v Praze?“ naléhá Prokop zatínaje pěstě. Doktor mlčí, ale něco v něm prudce
pracuje.
„Musím s ním mluvit,“ drtí Prokop. „Musím, slyšíte? Musím jet za ním, ještě
teď, hned! Kde je?“
Doktor něco přemílá čelistmi a jde ke dveřím.
„Kde je? Kde bydlí?“
„Nevím,“ rozkřikl se doktor nesvým hlasem a práskl dveřmi.
Prokop se obrátil k Anči. Seděla strnulá a upírala velikánské oči nikam.
„Anči,“ drmolil Prokop zimničně, „musíte mi říci, kde váš Jirka je. Já… já
musím za ním jet, víte? To je totiž… taková věc… Zkrátka jde tu o některé
věci… Já… Přečtěte si to,“ řekl honem a strkal jí před oči sežmolený kus
novin. Anči však viděla jenom jakési kruhy.
„To je můj vynález, rozumíte?“ vysvětloval nervózně. „Hledají mne, nějaký
Hanson – Kde je váš Jiří?“
„Nevíme,“ šeptala Anči. „Už dva… už dva roky nám nepsal –“
„Ach,“ utrhl se Prokop a vztekle zmačkal noviny. Děvče zkamenělo, jen oči jí
rostly a rostly a mezi pootevřenými rty jí dýchalo něco zmateně žalostného.
Prokop by se nejraději propadl. „Anči,“ rozřízl posléze mučivé ticho, „já se
vrátím. Já… za několik dní… Tohle je totiž vážná věc. Člověk… musí konečně
myslet… na své povolání. A má, víte, jisté… jisté povinnosti…“ (Bože, ten to
zkopal!) „Pochopte, že… Já prostě musím,“ křikl najednou. „Raději bych zemřel
než nejel, rozumíte?“
Anči jen maličko kývla hlavou. Ach, kdyby byla pokývla víc, byla by jí, bum,
hlava klesla na stůl v hlasitém pláči; ale takto se jí jen zalily oči a to
ostatní mohla ještě spolknout.
„Anči,“ bručel Prokop v zoufalých rozpacích a zachraňoval se ke dveřím, „ani
se nebudu loučit; hleďte, nestojí to za to; za týden, za měsíc tu budu zas…
Nu, hleďte –“ Ani se na ni nemohl podívat; seděla jako tupá, s plihými rameny,
očima nevidomýma a nosem, jenž nabíhal vnitřním pláčem; žalno ji vidět.
„Anči,“ pokusil se znovu a zas toho nechal. Nekonečná se mu zdála ta poslední
chvilka ve dveřích; cítil, že by měl něco ještě říci nebo něco udělat, ale
místo všeho vysoukal ze sebe jakési „na shledanou“ a trapně se vytratil.
Jako zloděj, po špičkách, opouštěl dům. Zaváhal ještě u dveří, za nimiž nechal
Anči. Bylo tam uvnitř ticho, jež ho sevřelo nevýslovnou trýzní. V domovních
dveřích se zarazil jako ten, kdo na něco zapomněl, a vracel se po špičkách do
kuchyně; bohudík, Nanda tam nebyla, i zamířil k poličce. „… ATIT!… adresu.
Carson, hl. p.“ To stálo na kuse novin, jež veselá Nanda cípatě nastříhala na
poličku. Tu tam pro ni položil plnou hrst peněz za všechnu její službu, a
zmizel.
Prokope, Prokope, tak nejedná člověk, který se chce za týden vrátit!
„To to ’de, to to ’de,“ skanduje vlak; ale lidské netrpělivosti už ani nestačí
jeho lomozný, drkotavý spěch; lidská netrpělivost se zoufale vrtí, pořád
vytahuje hodinky a kope kolem sebe v posunčině nervózy. Jedna, dvě, tři,
čtyři: to jsou telegrafní tyče. Stromy, pole, stromy, strážní domek, stromy,
břeh, břeh, plot a pole. Jedenáct hodin sedmnáct. Řepné pole, ženské v modrých
zástěrách, dům, psisko, jež si vzalo do hlavy předhonit vlak, pole, pole,
pole. Jedenáct hodin sedmnáct. Bože, což ten čas stojí? Raději na to nemyslet;
zavřít oči a počítat do tisíce; říkat si otčenáš nebo chemické vzorce. „To to
’de, to to ’de!“ Jedenáct hodin osmnáct. Bože, co počít?
Prokop se vytrhl. „KRAKATIT,“ padlo mu odněkud do očí, až se lekl. Kde je to?
Aha, to soused naproti čte noviny, a na zadní straně je zas ten inzerát.
„KRAKATIT! Ing. P. ať udá svou adresu. Carson, hl. p.“ Ať mi dá pokoj ten pan
Carson, myslí si Ing. P.; nicméně na nejbližší stanici shání všechny noviny,
co jich plodí požehnaná vlast. Bylo to ve všech, a ve všech stejně: „KRAKATIT!
Ing. P. ať udá…“ U všech rohatých, diví se Ing. P., to je po mně nějaká
sháňka! Nač mne potřebují, když jim to už Tomeš prodal?
Ale místo aby řešil tuto podstatnou záhadu, podíval se, není-li pozorován, a
vytáhl snad už posté onu povědomou roztrženou obálku. S všelijakými okolky,
jež mu působily silnou rozkoš odkladu, po různém potěžkávání a otáčení vyňal z
jejího nitra napěchovaného penězi zas onen dopis, onen drahocenný dopis psaný
písmem zralým a energickým. „Pane Tomši,“ četl znovu dychtivě, „toto nedělám
pro Vás, ale pro svou sestru. Šílí od té chvíle, kdy jste jí poslal svůj
strašlivý dopis. Chtěla prodat všechny své šaty a šperky, aby Vám poslala
peníze; musela jsem ji vší mocí zdržet, aby neprovedla něco, co by pak nemohla
utajit před svým mužem. Co Vám posílám, jsou mé vlastní peníze; vím, že je
přijmete bez zbytečných rozpaků, a prosím, abyste mi neděkoval. L.“ K tomu
chvatně připsáno: „Pro živého boha, nechte už M. na pokoji! Dala vše, co má;
dala vám více, než bylo její; trnu hrůzou, co bude, vyjde-li to najevo. Prosím
Vás pro vše na světě, nezneužívejte svého strašného vlivu na ni! Bylo by
příliš podlé, kdybyste –“ Zbytek věty byl přeškrtán, a následovalo ještě jedno
postskriptum: „Poděkujte za mne svému příteli, který vám toto doručí. Byl ke
mně nezapomenutelně laskav ve chvíli, kdy jsem nejvíc potřebovala lidské
pomoci.“
Prokopa zrovna drtila přemíra těžkého štěstí. Nebyla tedy Tomšova! A nikoho
neměla, o koho by se mohla opřít! Statečné děvče a ženerózní, čtyřicet tisíc
sehnala, aby zachránila svou sestru před… patrně před nějakou ostudou! Těchto
čtyřicet tisíc je z banky; jsou ještě opatřeny páskou, jak je vyzvedla, – u
čerta, proč na té pásce není jméno banky? A dalších deset tisíc vymetla kdoví
kde a jak; neboť jsou mezi nimi drobné bankovky, ubohé špinavé pětikoruny,
zchátralé hadříky z bůhvíjakých rukou, zmuchlané peníze ženských tobolek;
bože, co rozčilující sháňky ji muselo stát, než sehnala tuhle hrst peněz! „Byl
ke mně nezapomenutelně laskav…“ V tu chvíli by Prokop rozmlátil Tomše, bídníka
nesvědomitého a mrzkého; ale zároveň mu vše jaksi odpouštěl… neboť nebyla jeho
milenkou! Nebyla Tomšova: to přece přinejmenším znamená, že je to svatosvatě
anděl nejčistší a nejdokonalejší; a tu mu bylo, jako by se nějaká neznámá rána
zacelovala v jeho srdci prudce a zrovna bolestně.
Ano, nalézt ji; musím jí především… především vrátit tyhle její peníze (ani se
nestyděl za záminku tak průhlednou) a říci jí, že… že zkrátka… že může na mne
počítat, stran Tomše a vůbec… „Byl ke mně nezapomenutelně laskav.“ Prokop až
sepjal ruce: bože, co vše jsem odhodlán učinit, abych si zasloužil tahle slova
–
Ó-ó, jak ten vlak pomalu jede!


XV.

Jakmile přistál v Praze, hnal se do Tomšova bytu. U Muzea se zarazil:
Zatraceně, kde vlastně Tomeš bydlí? Šel jsem, ano, šel jsem tehdy, otřásán
zimnicí, na dráhu podle Muzea; ale odkud? Z které ulice? Zuře a klna bloudil
Prokop kolem Muzea, hledaje pravděpodobný směr; nenašel nic, i pustil se na
policejní ředitelství, oddělení dotazy. Jiří Tomeš, listoval zaprášený oficiál
v knihách, inženýr Tomeš Jiří, to je prosím na Smíchově, ulice ta a ta. Byla
to patrně stará adresa. Nicméně letěl Prokop na Smíchov do ulice té a té.
Domovník kroutil hlavou, když se ho ptal po Jiřím Tomši. Toť že tu ten jistý
bydlel, ale už víc než před rokem; kde bydlí teď, neví nikdo; ostatně nechal
tu po sobě všelijaké dluhy –
Zdrcen zalezl Prokop do nějaké kavárny. „KRAKATIT,“ padlo mu do očí na zadní
stránce novin. „Ing. P. ať udá svou adresu. Carson, hl. p.“ Nuže, jistě ví o
Tomšovi ten jistý Carson: už to tak je, že je mezi nimi jakási souvislost.
Dobře tedy, tady je lístek: „Carson, hlavní pošta. Přijďte zítra v poledne do
kavárny té a té. Ing. Prokop.“ Jen to napsal, a už ho napadla nová myšlenka:
totiž dluhy. Sebral se a utíkal k soudu, oddělení pro pohledávky. A hle, zde
tuze dobře znali adresu pana Tomše: celá hromada nedoručitelných obsílek,
soudních upomínek a tak dále; ale zdá se, že ten jistý Tomeš Jiří zmizel beze
stopy a zejména bez udání nynějšího pobytu. Přesto se vrhl Prokop za novou
adresou. Domovnice, osvěžena slušnou odměnou, hned poznala Prokopa, že tu
jednou přespal; i spustila přeochotně, že pan inženýr Tomeš je šejdíř a
darebák; dále, že zrovna tehdy v noci odejel a nechal tu jeho, pána, jí
domovnici na starost; že ona třikrát přišla nahoru se optat, potřebuje-li
čeho, ale že on, pán, jen pořád spal a mluvil ze spaní, a pak odpoledne
zmizel. A kdeže jářku je pan Tomeš? Inu, tenkrát tedy odejel a nechal tu
všecko stát a ležet a ještě se nevrátil; jen poslal peníze odněkud z ciziny,
ale je už zas dlužen za nový kvartál. Prý mu prodají v soudní dražbě svršky,
nepřihlásí-li se do konce měsíce. Nadělal prý dluhů asi za čtvrt miliónu, nu,
a utekl. Prokop podrobil výtečnou ženu křížovému výslechu: je-li jí co známo o
nějaké paničce, která prý měla s panem Tomšem poměr, kdo sem chodíval a
podobně. Domovnice nevěděla dohromady nic; co se týče ženských, chodilo jich
sem asi dvacet, takové se závojem na hubě, i jinačí, našminkované a všelijaké;
říkám vám, byla to ostuda po celé ulici. Prokop jí tedy zaplatil dlužný
kvartál ze svého, a za to dostal klíč od Tomšova bytu.
Bylo tam cítit jakousi ztuchlinu bytu dlouho neužívaného a skoro odumřelého.
Teprve teď si Prokop všiml divné nádhery místa, kde zápasil s horečkou. Všude
perské koberce a bucharské či jaké polštáře, na stěnách nahoty a gobelíny,
orient a klubovky, toaletní stůl subrety a koupelna prvotřídní prostitutky,
směs přepychu a sprostoty, smilstva a lajdáctví. A zde, uprostřed všech těch
svinstev, stála tehdy ona tisknouc k prsoum balíček; upírá čisté, hořeplné oči
k zemi, a teď, bože můj, je zvedá v statečné a ryzí důvěře… Proboha, co si
musela o mně myslet, když mne potkala v tomhle pelechu! Musím ji nalézt,
aspoň… aspoň proto, abych jí vrátil její peníze; i kdyby nešlo o nic jiného, o
nic většího… Je naprosto nutno ji nalézt!
To se lehko řekne; ale jak? Prokop si hryzl rty v úporném přemýšlení. Kdybych
aspoň věděl, kde hledat Jirku, říkal si; konečně padl na hromadu
korespondence, která tu čekala na Tomše. Většinou to byly, jak zřejmo,
obchodní dopisy, patrně samé účty. Pak několik soukromých listů, jež obracel a
očichával váhaje. Možná, možná že v některém je nějaká stopa, adresa nebo
cokoliv, co by jej vedlo za ním… nebo za ní! Hrdinně odolával pokušení otevřít
aspoň jeden dopis; ale byl tu tak sám za kalnými okny, a všechno tu zrovna
vydechuje nějakou mrzkou a tajnou hanebnost. A tu, rychle polykaje všechny
rozpaky, trhal Prokop obálky a četl list po listu. Účet za perské koberce, za
květiny, za tři psací stroje; velmi důtklivé upomínky, aby vyúčtoval zboží
dané do komise; jakési záhadné transakce týkající se koní, cizích valut a
dvaceti vagónů kulatého dříví kdesi u Kremnice. Prokop nevěřil svým očím;
podle těchto papírů byl Tomeš buď pašerák ve velkém, nebo agent s perskými
koberci, nebo valutní spekulant, nejspíš ale všecko troje; vedle toho
obchodoval s automobily, vývozními certifikáty, kancelářským nábytkem a patrně
vším možným. V jednom dopise je řeč o jakýchsi dvou miliónech, zatímco druhý,
usmolený a psaný tužkou, hrozí žalobou pro vylákanou starožitnost (staro bili
ring podědovi). Úhrnem to vypadalo na celou řadu podvodů, zpronevěru,
falšování vývozních listin a jiné paragrafy, pokud tomu Prokop vůbec rozuměl;
je prostě úžasné, že to dosud neprasklo. Jeden advokát stručně sděloval, že
firma ta a ta podala na pana Tomše trestní oznámení pro zpronevěru čtyřiceti
tisíc korun; ať se pan Tomeš ve vlastním zájmu dostaví do kanceláře atd.
Prokop se zhrozil; až tohle propukne, kam až stříkne hanba těchto nevýslovných
špinavostí? Vzpomněl si na tichý dům v Týnici a na tu, jež tady stála, zoufale
odhodlána zachránit toho člověka. I sebral celou tu obchodní korespondenci
firmy Tomeš a běžel ji spálit v kamnech. Bylo tam plno zuhelnatělých papírů.
Patrně sám Tomeš týmž způsobem zjednodušoval poměry, než ujel.
Dobrá, to byly obchodní papíry; zbývá ještě několik zcela soukromých dopisů
jemných anebo uboze umazaných, a nad nimi Prokop váhá znovu v palčivém studu.
U všech všudy, co jiného mohu udělat? Dusil se sice hanbou, ale trhal chvatně
další obálky. Zde pár lepkavých důvěrností, miláčku, vzpomínám, nová schůzka a
dost. Nějaká Anna Chválová s dojemnými pravopisnými chybami sděluje, že
Jeníček zemřel „na vyrážku“. Tady kdosi upozorňuje, že ví „něco, co by
zajímalo na policii“, ale že by dal se sebou mluvit, a že pan Tomeš „jistě ví,
jakou cenu má taková dikrétnost“; k tomu narážka na „ten dům v Břet. ul., kde
pan Tomeš ví, koho má hledat, aby to zůstalo pod pokličkou“. Zas něco o
jakémsi obchodě, o prodaných dluhopisech, podepsáno „Tvá Růža“. Táž Růža
sděluje, že její muž odejel. Táž ruka jako na čísle 1, dopis z lázní: nic než
kravské sentimentality, rozvalená erotika zralé a tučné blondýny, ocukrovaná
samými ach, výčitkami a krasocity, a k tomu „drahouši“ a „divochu“ a podobné
ohavnosti; Prokopovi se z toho obracel žaludek. Německý dopis, písmeno „G.“,
valutní obchod, prodej ty papíry, erwarte Dich, P. S. Achtung, K. aus Hamburg
eingetroffen. Táž „G“, uražený a chvatný dopis, mrazivé vykání, vraťte těch
deset tisíc, sonst wird K. dahinterkommen, hm. Prokop se k smrti styděl vnikat
do navoněného přítmí těchto spodničkových záležitostí, ale teď už se nelze
zastavit. Konečně čtyři dopisy signované M.: listy slzavé, horečné a trapné, z
nichž dýchala těžká a vášnivá historie nějaké slepé, dusné, otrocké lásky.
Byly tu úpěnlivé prosby, plazení v prachu, zoufalé inkriminace, strašné
sebenabízení a ještě strašnější sebetrýzeň; zmínka o dětech, o muži, nabídka
nové půjčky, nejasné narážky a přespříliš jasná zbědovanost ženy usmýkané
láskou. Tohle tedy je její sestra! Prokopovi bylo, jako by viděl před sebou
výsměšná a krutá ústa, pichlavé oči, panskou a zpupnou, sebevědomou,
sebejistou hlavu Tomšovu: byl by do ní udeřil pěstí. Avšak nic platno: tato
žalostně obnažená láska ženina mu neřekla toho nejmenšího o… o té druhé, jež
dosud nemá pro něho jména a kterou jest mu hledati.
Nezbývá tedy než nalézti Tomše.


XVI.

Nalézti Tomše: lidi, jako by tohle bylo tak lehké! Prokop provedl znovu
generální prohlídku celého bytu; řádil ve všech skříních i zásuvkách,
nenacházeje krom prašiviny starých účtů, milostných dopisů, fotografií a
jiného mládeneckého neřádu nic, co by jakkoliv osvětlilo Tomšovu záležitost.
Nu ovšem, má-li někdo tolik másla na hlavě, dovede už důkladně zmizet!
Znovu vyslechl domovnici; zvěděl sice záplavu všelijakých historek, ale nic,
co by ho uvedlo na stopu. Šel na pana domácího, odkudže poslal Tomeš ty peníze
z ciziny. Bylo mu vyslechnouti celé kázání nevrlého a dosti nepříjemného
staříka, který trpěl všemi možnými katary a nadával na zkaženost dnešních
mladých pánů. Za cenu nadlidské trpělivosti zvěděl konečně jen to, že řečené
peníze neposlal pan Tomeš, nýbrž jakýsi směnárník na konto Drážďanské banky
„auf Befehl des Herrn Tomes“. Rozběhl se k advokátovi, který měl, jak výše
sděleno, jistou rozpracovanou záležitost s pohřešovaným. Advokát se zbytečně
halil v profesionální tajemství; ale když Prokop hloupě vybleptl, že má panu
Tomšovi doručit nějaké peníze, oživl advokát a žádal, aby je složil do jeho
rukou; i dalo Prokopovi mnoho práce, aby se z toho vymotal. To jej poučilo,
aby nepátral po Tomšovi u lidí, kteří s ním měli jakékoliv obchodní řízení.
Na nejbližším rohu zůstal stát: Co teď? Zbývá jen Carson. Neznámá veličina,
jež o něčem ví a něco chce. Dobrá, tedy Carson. Prokop nahmatal v kapse
lístek, jejž zapomněl poslat, a rozběhl se na poštu.
Ale u poštovní schránky mu klesla ruka. Carson, Carson, – ano, ale tomu jde o
cosi, co… také není maličkost. U čerta, ten chlap něco ví o Krakatitu a má za
lubem – inu bůhsámví co. Proč vůbec mne shání? Patrně Tomeš neví vše; nebo
nechtěl vše prodat; nebo si klade nestydaté podmínky, a já osel mám být
lacinější. Tak asi to je; ale (a tu se Prokop poprvé zhrozil dosahu věci) což
je vůbec možno vyrukovat s Krakatitem ven? Především by se muselo u sta hromů
pořádně vědět, co to dělá a k čemu je to dobré, jak se s tím zachází a kdesi
cosi; Krakatit, holenku, to není šňupavý tabák nebo zasýpací prášek pro děti.
A za druhé, za druhé snad je to vůbec… příliš silný tabák pro tento svět.
Představme si, co by se s tím mohlo natropit… řekněme ve válce. Prokopovi
začalo být z celé věci až úzko. Který čert sem nese toho zatraceného Carsona?
Prokristapána, musí se za každou cenu zabránit –
Prokop se chytil za hlavu tak, až se zastavovali lidé. Vždyť, proboha,
zanechal tam nahoře, ve svém laboratorním baráku u Hybšmonky, v porcelánové
dózi skoro patnáct deka Krakatitu! tedy zrovna dost, aby to mohlo rozmlátit já
nevím co, celé hejtmanství! Přímo ztuhl úděsem, a pak se pustil tryskem k
tramvaji: jako by teď ještě záleželo na těch několika minutách! Trpěl pekelně,
než se tramvaj dovlekla na druhý břeh; pak ztekl cvalem košířskou stráň a
uháněl k svému baráku. Bylo zamčeno, a Prokop marně hledal po kapsách něco
podobného klíči; i rozhlédl se soumrakem jako zloděj, rozbil okenní tabulku,
otevřel závory a vlezl oknem domů.
Jen rozškrtl sirku a už viděl, že je co nejmetodičtěji vyloupen. Totiž peřiny
a takové krámy tu zůstaly; ale všechny lahvičky, kelímky a zkumavky, crushery,
hmoždíře, misky a přístroje, lžíce a váhy, celá jeho primitivní chemická
kuchyně, vše, co obsahovalo jeho pokusné hmoty, vše, na čem mohla být jen
usazenina či nálet nějaké chemikálie, vše zmizelo. Pryč je porcelánová dóza s
Krakatitem. Vytrhl zásuvku stolu: veškeré jeho zápisky a záznamy, každý
počmáraný útržek papíru, sebemenší památka dvanáctileté pokusné práce, vše
bylo to tam. Dokonce i s podlahy byly seškrabány skvrny a stopy jeho práce, a
jeho pracovní hazuka, ta stará, potřísněná, lučebninami zrovna zkornatělá
halena byla pryč. Hrdlo se mu sevřelo návalem pláče. Tohle tedy, tohle mi
udělali!
Dlouho do noci seděl na svém vojanském kavalci a strnule zíral do vypleněné
pracovny. Chvílemi se utěšoval, že si snad vzpomene na vše, co během dvanácti
let psal do svých poznámek; ale když namátkou vybral některý experiment a
chtěl si jej popaměti v hlavě zopakovat, nemohl z místa přes úsilí
nejzoufalejší; tu hryzal si rozbité prsty a sténal.
Náhle se probudil zarachocením klíče. Je čiročiré ráno a do pracovny jakoby
nic vchází cizí člověk a rovnou ke stolu. Tam teď sedí s kloboukem na hlavě,
bručí a pečlivě oškrabuje na stole zinek. Prokop se posadil na kavalci a
vyhrkl: „Člověče, co tu chcete?“
Člověk se obrátil nesmírně překvapen a beze slova koukal na Prokopa.
„Co tu chcete?“ opakoval Prokop podrážděně. Chlapík nic; ještě si ke všemu
nasadil skla a brejlil na Prokopa s ohromným zájmem.
Prokop zaskřípal zuby, neboť se v něm už vařila hrozná nadávka. Ale tu
človíček vlídně zazářil, vymrštil se ze židle a vypadal najednou, jako by
radostně vrtěl ocasem. „Carson,“ řekl honem a spustil po německu: „Bože, to
jsem rád, že jste se vrátil! Četl jste můj inzerát?“
„Četl,“ odpovídal Prokop tvrdou a centovou němčinou. „A co tu hledáte?“
„Vás,“ povídal host báječně potěšen. „Víte, že vás honím už po šest neděl?
Všecky noviny, všecky detektivní ústavy, haha, pane! co tomu říkáte? Hergot,
to mám radost! Jak se vede? Zdráv?“
„Proč jste mne vykradl?“ ptal se Prokop mračně.
„Jak prosím?“
„Proč jste mne vykradl!“
„Ale pane inženýre,“ sypal blažený mužík pranic nedotčen. „Co to říkáte?
Vykradl! Carson! To je ohromné, hahaha!“
„Vykradl,“ opakoval Prokop umíněně.
„Tatata,“ protestoval pan Carson. „Schoval. Všecko uložil. Pane, jak jste to
tu mohl nechat ležet? Někdo vám to mohl ukrást, ne? Co? Ovšem že mohl, pane.
Ukrást, prodat, publikovat, že? To se rozumí, pane. Mohl. Ale já jsem vám to
schoval, rozumíte? Čestné slovo. Proto jsem vás hledal. Všecko vrátím. Všecko.
To jest,“ dodával váhavě, a pod zářivými brýlemi to ocelově utkvělo. „Totiž…
budete-li rozumný. Vždyť my se dohodneme, co?“ dodával rychle. „Musíte se
habilitovat. Ohromná kariéra. Atomové výbuchy, rozbití prvků, báječné věci.
Věda, především věda! My se dohodneme, že? Čestné slovo, dostanete všecko
zpátky. Tak.“
Prokop mlčel, ohromen tímto přívalem slov, zatímco pan Carson házel rukama a
kroužil po pracovně náramně rozradován. „Všecko, všecko jsem vám schoval,“
mlel jaře. „Každou třísku z podlahy. Roztříděno, uloženo, s vinětou, pod
pečetí. Haha, mohl jsem se vším ujet, že? Ale já jsem poctivec, pane. Všecko
vrátím. Musíme se domluvit. Ptejte se na Carsona. Rodilý Dán, dříve docent v
Kodani. Taky jsem dělal vědu, božskou vědu. Jak to řekl Schiller? Dem einen
ist sie – ist sie – Už nevím, ale je to něco o vědě; švanda, že? Nu, ještě mi
neděkujte. Až později. Tak.“
Prokopovi sice ani nenapadlo děkovat, ale pan Carson zářil jako šťastný
dobrodinec. „Na vašem místě,“ drmolil nadšeně, „na vašem místě bych si zařídil
–“
„Kde je teď Tomeš?“ pře rušil ho Prokop.
Pan Carson vysunul zkoumavý pohled. „Nu,“ vycedil obezřele, „my o něm víme. Eh
co,“ obrátil hbitě. „Zařídíte si… zařídíte si největší laboratoř světa.
Nejlepší přístroje. Světový ústav destruktivní chemie. Máte pravdu, katedra je
hloupost. Odříkávat staré věci, no ne? Škoda času. Zařiďte si to po
amerikánsku. Ohromný ústav, brigáda asistentů, všechno, co chcete. A o peníze
se nemusíte starat. Punktum. Kde snídáte? Já bych vás ohromně rád pozval.“
„Co vlastně chcete?“ vydralo se z Prokopa.
Tu sedl pan Carson na kavalec vedle něho, vzal ho nesmírně vřele za ruku a
povídal najednou docela jiným hlasem: „Jen se neplašte. Můžete vydělat celou
hromadu miliónů.“


XVII.

Prokop s úžasem vzhlédl na pana Carsona. Kupodivu, teď už to nebyla ta
mopsličí tvář lesknoucí se blahem; všecko zvážnělo a zpřísnělo na horlivém
mužíkovi, oči zapadly pod těžkými víčky a jen chvílemi se zařízly matným
břitem. „Nebuďte blázen,“ pronesl důrazně. „Prodejte nám Krakatit, a je to.“
„Jak vůbec víte…,“ zahučel Prokop.
„Všecko vám povím. Čestné slovo, všecko. Byl u nás pan Tomeš. Přinesl patnáct
deka a formuli. Bohužel nepřinesl taky postup. Ani on, ani naši chemikové
dosud na to nepřišli, jak to dostat dohromady. Nějaký trik, že?“
„Ano.“
„Hm. Třeba se na to přijde i bez vás.“
„Nepřijde.“
„Pan Tomeš… něco o tom ví, ale dělá s tím tajnosti. Pracoval u nás při
zamčených dveřích. Je hrozně špatný chemik, ale je chytřejší než vy. Aspoň
nežvaní o tom, co ví. Proč jste mu to říkal? Neumí nic, jen pumpovat zálohy.
Měl jste přijít sám.“
„Já ho k vám neposlal,“ bručel Prokop.
„Aha,“ spustil pan Carson, „ohromně zajímavé. K nám přišel ten váš pan Tomeš
–“
„Kam vlastně?“
„K nám. Továrny v Balttin. Znáte?“
„Neznám.“
„Zahraniční podnik. Báječně moderní. Pokusná laboratoř s novými třaskavinami.
Děláme keranit, metylnitrát, žlutý prach a takové věci. Hlavně armádní, víte?
Tajné patenty. Vy nám prodáte Krakatit, co?“
„Ne. A Tomeš je tam u vás?“
„Aha, pan Tomeš; počkejte, to je švanda. Tak tedy k nám přijde a povídá: Tohle
je odkaz mého přítele, geniálního chemika Prokopa. Umřel mně v náruči a
posledním dechem, haha, mně svěřil, hahaha, ohromné, co?“
Prokop se jen křivě usmál. „A je Tomeš dosud… v Balttinu?“
„Počkejte. To se rozumí, nejdřív jsme ho zadrželi… jako špióna. K nám jich
chodí spousta, víte? A ten prášek, Krakatit, jsme dali přezkoušet.“
„Výsledek?“
Pan Carson zvedl ruce k nebi. „Bá-báječný!“
„Jaká je detonační rychlost? Jaké jste našli Q? Jaké t? Čísla!“
Pan Carson spustil ruce, až to pláclo, a vyvalil užasle oči: „Člověče, jakápak
čísla! První pokus… padesát procent škrobu… a crusher gauge se roztrhl na
střepy; jeden inženýr a dva laboranti… taky na střepy. Věřil byste? Pokus
číslo dvě: Trauzlův blok, devadesát procent vazelíny, a bum! sebralo to
střechu a jeden dělník zabit; z bloku zůstal jen škvarek. Tak se do toho
pustili vojáci; smáli se nám… že to umíme jako… vesnický kovář. Dali jsme jim
trochu; nacpali to do dělové hlavně, s rozemletým dřevěným uhlím. Ohromný
výsledek. Sedm kanonýrů i se setníkem… Jednu nohu pak našli tři kilometry
daleko. Za dva dny dvanáct mrtvých, tu máte čísla, haha! Báječné, co?“
Prokop chtěl něco říci, ale spolkl to. Dvanáct mrtvých za dva dny, u čerta!
Pan Carson si hladil kolena a zářil. „Třetí den jsme si dali pokoj. Víte, dělá
to špatný dojem, když… je mnoho takových případů. Dali jsme jen flegmatizovat
Krakatit… asi tři decigramy… v glycerínu a podobně. Prase laborant nechal asi
špetku volně ležet, a v noci, když byla laboratoř zamčena –“
„– to vybuchlo,“ vyhrkl Prokop.
„Ano. V deset třicet pět. Laboratoř byla na třísky, krom toho asi dva objekty…
Vzalo to s sebou nějaké tři tuny metylnitrátu Probst – Zkrátka asi šedesát
mrtvých, no. To se ví, náramné vyšetřování a kdesi cosi. Ukázalo se, že nikdo
v laboratoři nebyl, že to muselo patrně vybuchnout –“
„– samo od sebe,“ doplnil Prokop sotva dýchaje.
„Ano. Vám také?“
Prokop ponuře kývl.
„Tak vidíte,“ řekl pan Carson rychle. „Není to k ničemu. Tuze nebezpečná věc.
Prodejte nám to, a je to, máte to z krku. Co byste s tím dělal?“
„A co byste s tím dělali vy?“ vycedil Prokop.
„My už… my jsme na to zařízeni. Božínku, pár těch mrtvých – Ale vás by byla
škoda.“
„Ale Krakatit v porcelánové krabici nevybuchl,“ mínil Prokop úporně
přemýšleje.
„Chválabohu ne. Kdepak!“
„A bylo to v noci,“ přemýšlel Prokop dál.
„V deset třicet pět. Přesně.“
„A… ta špetka Krakatitu ležela na zinkovém… na kovovém plechu,“ tvrdil Prokop
dále.
„Ten na to nemá vliv,“ vybleptl mužík trochu zmaten, kousl se do rtu a jal se
přecházet po laboratoři. „Bylo to… bylo to asi jen okysličování,“ zamlouval to
po chvíli. „Nějaký chemický proces. Směs s glycerínem taky nevybuchla.“
„Protože není vodivá,“ zamumlal Prokop. „Nebo nemůže ionizovat, já nevím.“
Pan Carson se nad ním zastavil s rukama na zádech. „Vy jste moc chytrý,“ řekl
uznale. „Musíte dostat mnoho peněz. Tady je vás škoda.“
„Je Tomeš pořád v Balttinu?“ ptal se Prokop, vší silou se přemáhaje, aby to
vyznělo lhostejně.
Panu Carsonovi to nějak blýsklo pod brejlemi. „Máme ho na očích,“ řekl
vyhýbavě. „Sem se už jistě nevrátí. Přijeďte k nám… třeba ho najdete, když –
tak – tuze – chcete,“ slabikoval důrazně.
„Kde je?“ opakoval Prokop tvrdohlavě, dávaje najevo, že jinak nemluví.
Pan Carson zamával rukama jako pták. „No, utekl,“ dodával na Prokopův
nechápavý pohled.
„Utekl?“
„Vypařil se. Špatně hlídán, tuze chytrý. Zavázal se, že sestrojí celý
Krakatit. Zkoušel to… asi šest neděl. Stál nás hrozné peníze. Pak zmizel,
lump. Nevěděl si asi rady, co? Neumí nic.“
„A kde je?“
Pan Carson se naklonil k Prokopovi. „Lump. Teď nabízí Krakatit jinému státu.
Přitom jim přinesl taky náš metylnitrát, ten taškář. Sedli mu na lep, teď dělá
u nich.“
„Kde?“
„Nesmím říci. Na mou čest, nesmím. A když nám pláchl, jel jsem, haha,
navštívit váš hrob. Pieta, co? Geniální chemik, a nikdo ho tady nezná. To byla
práce, člověče. Musel jsem inzerovat jako blbec. To se ví, že si toho všimli…
ti druzí, víte? Rozumíte mi?“
„Ne.“
„Tak se pojďte podívat,“ řekl pan Carson čile a zamířil k protější stěně.
„Tady,“ řekl a ťukal na prkno.
„Co je?“
„Kulka. Někdo tu byl.“
„A kdo po něm střelil?“
„No já přece. Kdybyste byl sem lezl… oknem… takhle před čtrnácti dny, byl by
vás třeba někdo… moc šeredně vzal na mušku.“
„Kdo?“
„To je jedno, ten nebo onen stát. Tady si, holenku, podávaly dvéře tuze velké
mocnosti. A vy jste zatím někde, haha, chytal ryby, co? Báječný chlapík! Ale
poslyšte, drahoušku,“ řekl najednou starostlivě, „neračte raději chodit sám.
Nikdy a nikde, rozumíte?“
„Nesmysl!“
„Počkejte. Žádný granátník. Velmi nenápadní lidé. Dnes se to dělá… náramně
diskrétně.“ Pan Carson se zastavil u okna a bubnoval na sklo. „Nemáte ponětí,
co jsem na svůj inzerát dostal dopisů. Asi šest Prokopů se hlásilo… Pojďte se
honem podívat!“
Prokop přistoupil k oknu. „Co je?“
Pan Carson jen ukázal krátkým prstem na silnici. Motal se tam na velocipédu
nějaký mládenec v zoufalém zápase s rovnováhou, přičemž každé kolo jevilo
umíněnou náklonnost jet jiným směrem. Pan Carson tázavě pohlédl na Prokopa.
„Učí se asi jezdit,“ mínil Prokop nejistě.
„Náramný nešika, že?“ řekl pan Carson a otevřel okno. „Bob!“
Mladík na kole se zastavil jako přibitý: „Yessr.“
„Go to the town for our car!“
„Yessr.“ A přišlápnuv pedály svištěl mladý cyklista k městu.
Pan Carson se obrátil od okna. „Ir. Velrni obratný hoch. Co jsem chtěl říci?
Aha. Tedy asi šest Prokopů se mi hlásilo – schůzky na různých místech, zejména
v noci – švanda, že? Přečtěte si tenhle lístek.“
„Přijďte zítra v deset večer do mé laboratoře, ing. Prokop,“ četl Prokop jako
ve snu. „Ale vždyť je to… bezmála… mé písmo!“
„Tak vidíte,“ zubil se pan Carson. „Holenku, tady je horká půda. Prodejte to,
ať máte pokoj!“
Prokop zavrtěl hlavou.
Pan Carson na něm spočinul těžkým, neodbytným pohledem. „Můžete žádat…
řekněme… dvacet miliónů. Prodejte nám Krakatit.“
„Ne.“
„Dostanete všecko zpátky. Dvacet miliónů. Člověče, prodejte to!“
„Ne,“ řekl Prokop těžce. „Nechci mít co dělat… s vašimi válkami. Nechci.“
„Co máte tady? Geniální chemik a… bydlí v prkenné boudě! Krajani! Já to
neznám. Velký člověk nemá žádné krajany. Nekoukejte na nic! prodejte to a –“
„Nechci.“
Pan Carson strčil ruce do kapes a zívl. „Války! Myslíte, že jim zabráníte?
Pche! Prodejte a nestarejte se k čemu. Vy jste učenec… co je vám po ostatním?
Války! Jděte, nebuďte směšný. Pokud mají lidé nehty a zuby –“
„Neprodám,“ drtil Prokop mezi zuby.
Pan Carson pokrčil rameny. „Jak chcete. Najdeme si to sami. Nebo to najde
Tomeš. Taky dobře.“
Bylo chvíli ticho. „Mně je to jedno,“ ozval se pan Carson. „Je-li vám to
milejší, pojedeme s tím do Francie, do Anglie, kam chcete, třeba do Číny. My
oba, víte? Tady by nám to nikdo nezaplatil. Byl byste osel, kdybyste to prodal
za dvacet miliónů. Spolehněte se na Carsona. Tak co?“
Prokop rozhodně zavrtěl hlavou.
„Charakter,“ prohlásil pan Carson uznale. „Všecka čest. To se mně ohromně
líbí. Poslyšte, vám to řeknu. Naprosté tajemství. Ruku na to.“
„Neptám se po vašich tajemstvích,“ bručel Prokop.
„Bravo. Diskrétní člověk. Můj typ, pane.“


XVIII.

Pan Carson si sedl a zapálil si velmi tlusté cigáro, načež usilovně přemítal.
„Tjaja,“ řekl po chvíli. „Tak vám to taky vybuchlo. Kdy to bylo? Datum.“
„… Nevím už.“
„Den v týdnu?“
„… Nevím. Myslím… dva dny po neděli.“
„Tedy v úterý. A v kolik hodin?“
„Asi… po desáté večer.“
„Správně.“ Pan Carson zamyšleně vyfukoval kouř. „Nám to poprvé vybuchlo… jak
vy se račte vyjadřovat, ,samo od sebe‘… v úterý v deset třicet pět. Viděl jste
přitom něco?“
„Ne. Já jsem spal.“
„Aha. Taky to vybuchuje v pátek, kolem půl jedenácté. V úterý a v pátek. My
jsme to zkoušeli,“ vysvětloval na Prokopův vyjevený pohled. „Nechali jsme
volně ležet miligram Krakatitu a hlídali jsme to ve dne v noci. Vybuchovalo to
v úterý a v pátek, o půl jedenácté. Sedmkrát. Jednou taky v pondělí, v deset
dvacet devět. Tak.“
Prokop se omezil na to, že tiše žasnul.
„To proběhne Krakatitem taková modrá jiskra,“ dodával pan Carson zahloubaně,
„a pak to exploduje.“
Bylo tak ticho, že Prokop slyšel tikání Carsonových hodinek.
„Tjaja,“ vzdychl pan Carson a projel si zoufale zrzavý kartáč vlasů.
„Co to znamená?“ vyrazil Prokop.
Pan Carson jen trhl rameny. „A co vy,“ řekl, „co vy jste si vlastně myslel,
když vám to… ,samo od sebe‘… vybuchlo? Nu?“
„Nic,“ uhýbal Prokop. „Nepřemýšlel jsem o tom… tak dalece.“
Pan Carson zabručel něco urážlivého.
„Totiž,“ opravoval se Prokop, „tehdy mě napadlo, že to dělají… třeba…
elektromagnetické vlny.“
„Aha. Elektromagnetické vlny. My jsme si to taky mysleli. Výborná myšlenka,
jenže pitomá. Bohužel docela pitomá. Tak.“
Nyní si Prokop opravdu nevěděl rady.
„Předně,“ uvažoval pan Carson, „bezdrátové vlny neběhají po světě jenom v
úterý a v pátek o půl jedenácté, že? A za druhé, člověče, to si můžete myslet,
že jsme to s nimi hned vyzkoušeli. S krátkými, s dlouhými, se všemi možnými. A
váš Krakatit si z nich nedělal tohle,“ ukazoval na nehtu něco mizivě
nepatrného. „Ale v úterý a v pátek… o půl jedenácté… si umane ,sám od sebe‘
explodovat. A víte co ještě?“
Prokop to ovšem nevěděl.
„Ještě tohle. Od nějaké doby… asi půl roku nebo tak… mají evropské bezdrátové
stanice děsný dopal. Víte, něco jim ruší hovory. Docela pravidelně. Náhodou…
vždycky v úterý a v pátek od půl jedenácté v noci. Pravíte?“
Prokop nepravil nic, jen si mnul čelo.
„Nu ano, v úterý a v pátek. Říkají tomu smazané hovory. Začne to telegrafistům
práskat do uší, a tu to máme; hoši se z toho mohou zbláznit. Trapné, co?“ Pan
Carson si sundal brejle a jal se je čistit s velkými okolky. „Nejdřív… nejdřív
mysleli, že to jsou nějaké magnetické bouře či co. Ale když viděli, že to
úřaduje… pravidelně… v úterý a v pátek… Zkrátka Marconi, TSF, Transradio a
nějaká ministerstva pošt a maríny, obchodu, vnitra a já nevím čeho všeho
vyplatí dvacet tisíc liber chytrákovi, který tomu přijde na kobylku.“ Pan
Carson si nasadil zas brejle a vesele vykoukl. „Myslí se, že existuje nějaká
nezákonná stanice, která se baví tím, že v úterý a v pátek smazává hovory.
Konina, že? Soukromá stanice, která by jen tak pro švandu posílala nejmíň sto
kilowattů do vzduchu! Fi!“ Pan Carson si odplivl.
„V úterý a v pátek,“ ozval se Prokop, „tedy současně… zároveň…“
„Divné, že?“ šklebil se pan Carson. „Já to mám, panečku, napsáno: V úterý dne
toho a toho v deset třicet pět a několik vteřin porucha na všech stanicích od
Revalu a tak dále. A nám v tu samou vteřinu ,sama od sebe‘, jak vy račte
říkat, exploduje jistá část vašeho Krakatitu. Eh? Co? Detto příští pátek v
deset dvacet sedm a několik vteřin porucha a výbuch. Item příští úterý v deset
třicet výbuch a porucha. A tak dále. Výjimečně, jaksi proti programu taky
jednou porucha v pondělí v deset dvacet devět třicet sekund. Detto výbuch.
Klape to na vteřinu. Osmkrát v osmi případech. Špás, že? Co o tom soudíte?“
„N… nevím,“ mumlal Prokop.
„Tak teda ještě něco,“ spustil pan Carson po delším hloubání. „Pan Tomeš u nás
pracoval. Neumí nic, ale něco ví. Pan Tomeš si dal do laboratoře postavit
vysokofrekvenční generátor a zamkl nám dvéře před nosem. Lump. Jakživ jsem
neslyšel, že by se v obyčejné chemii pracovalo s vysokofrekvenčními mašinami,
co? Co byste řekl?“
„Nu… ovšem,“ uhýbal Prokop s neklidným pohledem na svůj vlastní zánovní
agregát postavený v koutě.
Pan Carson chytl čile tento pohled. „Hm,“ řekl, „taky tu máte takovou hračku,
že? Pěkný transformátorek. Co vás stál?“
Prokop se zamračil, ale pan Carson počal tiše zářit. „Tak si myslím,“ povídal
s rostoucí blažeností, „že by to byla báječná věc, kdyby se povedlo v nějaké
hmotě… dejme tomu pomocí vysoké frekvence… v jiskrovém poli nebo jak…
rozkmitat, rozviklat, uvolnit vnitřní strukturu tak, že by stačilo ťuknout z
dálky… nějakými vlnami… výboji… oscilacemi nebo čertví čím, aby se ta hmota
rozpadla, co? Bum! Na dálku! Co tomu říkáte?“
Prokop neřekl nic, a pan Carson cucaje s rozkoší cigáro se na něm jen pásl.
„Já nejsem elektrikář, víte?“ začal po chvíli. „Mně to vysvětloval jeden
učenec, ale ať se propadnu, jestli jsem to pochopil. Ten chlap šel na mne s
elektrony, ionty, elementárními kvanty a já ani nevím, jak to jmenoval; a
nakonec to katedrové světlo prohlásilo, že to zkrátka a dobře vůbec není
možno. Člověče, vy jste si dal! Udělal jste něco, co podle světové autority
není možno…“
„Tak já jsem si to vyložil sám,“ pokračoval, „jen tak ševcovsky. Někdo si
dejme tomu vezme do hlavy… udělat vratkou sloučeninu… z jisté olovnaté soli.
Dotyčná sůl je neřád; ne a ne se slučovat, že? Tak ten chemik zkouší všechno
možné… jako blázen; a tu si dejme tomu vzpomene, že v lednovém čísle The
Chemist bylo povídání o tom, že dotyčná flegmatická sůl je báječný koherer…
detektor pro elektrické vlny. Dostane nápad. Pitomý a geniální nápad, že by
snad mohl tu zatracenou sůl přivést elektrickými vlnami do lepší nálady, ne?
povzbudit ji, roztancovat ji, natřást ji jako peřinu, že? Tja, nejlepší nápady
dostane člověk z blbosti. Tak tedy sežene takový komický transformátorek a dá
se do toho; co prováděl, to je zatím jeho tajemství, ale koneckonců… dostane
hledanou sloučeninu. Ať mne čert, dostane ji. Nejspíš to nějak slepil tou
oscilací. Člověče, já se budu muset na stará kolena učit fyzice; říkám boty,
že?“
Prokop zabručel něco docela nesrozumitelného.
„To nevadí,“ prohlásil pan Carson spokojeně. „Jen když to zatím drží
dohromady; já jsem pitomec, já si představuju, že to dostalo nějakou
elektromagnetickou strukturu nebo co. Kdyby se nějak porušila, tak… se to
rozpadne, že? Naštěstí asi deset tisíc řádných radiostanic a několik set
nezákonných udržuje v naší pozemské atmosféře takové elektromagnetické klima,
takovou eh eh oscilační lázeň, která jde zrovna k duhu té struktuře. A tak to
drží dohromady…“
Pan Carson se maličko zamyslil. „A teď,“ začal zase, „teď si představte, že
nějaký ďábel nebo holomek na tomto světě má prostředek, kterým může dokonale
rušit elektrické vlny. Prostě je smazat či co. Představte si, že to – bůhsámví
proč – tropí pravidelně v úterý a v pátek o půl jedenácté v noci. V tu minutu
a vteřinu se poruší na tomto světě bezdrátové spojení; ale v tu minutu a
vteřinu se nejspíš něco stane taky v té… labilní sloučenině, pokud není zrovna
izolována… dejme tomu v… v porcelánové krabici; něco se v ní poruší… jaksi v
ní lupne, a ona se… ona se…“
„… rozpadne,“ vyhrkl Prokop.
„Ano, rozpadne se. Exploduje. Zajímavé, co? Jeden učený pán mně to vysvětloval
– hrome, jak to říkal? že – že prý –“
Prokop vyskočil a popadl pana Carsona za kabát. „Poslyšte,“ koktal hrozně
rozčilen, „kdyby se tedy… Krakatit… roztrousil dejme tomu tady… nebo kdekoliv…
prostě po zemi…“
„… tedy to nejbližšího úterku nebo pátku o půl jedenácté vyletí do povětří.
Tja. Člověče, neuškrťte mne.“
Prokop pustil pana Carsona a přebíhal po světnici hryže si hrůzou prsty. „To
je jasné,“ mručel, „to je jasné! Nikdo nesmí Krakatit vy-vyrá–“
„Krom pana Tomše,“ namítl Carson skepticky.
„Dejte mi pokoj,“ utrhl se Prokop. „Ten na to nepřijde!“
„Nu,“ mínil pan Carson s pochybami, „já nevím, kolik jste mu toho řekl.“
Prokop se zastavil jako vražen do země. „Představte si,“ kázal horečně,
„představte si dejme tomu… vvválku! Kdo má v rukou Krakatit, může… může…
kdykoli chce…“
„Zatím jen v úterý a v pátek.“
„… vyhodit do povětří… celá města… celé armády… a všecko! Stačí… stačí jen
roz-trousit – Dovedete si to představit?“
„Dovedu. Báječně.“
„A proto… v zájmu světa… nikdy… nedám nikdy!“
„V zájmu světa,“ bručel pan Carson. „Víte, v zájmu světa by hlavně bylo přijít
na kloub té – té –“
„Čemu?“
„Té zatracené stanici anarchistů.“


XIX.

„Vy tedy myslíte,“ koktal Prokop, „že… že snad…“
„My tedy víme,“ přerušil ho Carson, „že jsou na světě neznámé vysílací a
přijímací stanice. Že si pravidelně v úterý a v pátek říkají nejspíš něco
jiného než dobrou noc. Že disponují nějakými nám dosud neznámými silami,
výboji, oscilacemi, jiskrami, paprsky nebo čím zatraceným a… a zkrátka
nezachytitelným. Anebo nějakými antivlnami, antioscilacemi nebo jak to k čertu
nazvat, něčím, co prostě přerušuje nebo smazává naše vlny, rozumíte?“ Pan
Carson se rozhlédl po laboratoři. „Aha,“ řekl a popadl kus křídy. „Buď je to
takhle,“ povídal rýsuje na podlaze asi půlloketní šipku křídou, „nebo takhle,“
a přitom pokřídoval celý kus prkna a do toho vmázl nasliněným prstem temnou
čáru. „Tak nebo tak, rozumíte? Pozitivně nebo negativně. Buď posílají nějaké
nové vlny do našeho média, nebo vrhají do našeho kmitajícího, skrznaskrz
protelegrafovaného prostředí umělé pauzy, chápete? Obojím způsobem se dá
pracovat… bez naší kontroly. Obojí je dosud… technicky i fyzikálně… naprostá
záhada. Zatraceně,“ křikl pan Carson v náhlém vzteku a praštil křídou, až se
rozstříkla, „tohle je příliš! Posílat neznámými silami tajné depeše záhadnému
adresátovi! Kdo tohleto dělá? Co teda myslíte?“
„Třeba Marťané,“ nutil se Prokop zažertovat; ale opravdu, nebylo mu do žertu.
Pan Carson po něm vražedně vykoukl, ale pak se rozřehtal přímo koňsky. „Dejme
tomu, že Marťané. Bájecně! Dejme tomu, mistře. Ale dejme tomu, že spíš někdo
na zemi. Dejme tomu, že nějaká pozemská moc rozesílá své tajné instrukce.
Dejme tomu, že má tuze vážné příčiny vyhnout se lidské kontrole. Dejme tomu,
že je nějaká… mezinárodní služba nebo organizace nebo čertví co, a že to má k
dispozici neznámé síly, tajemné stanice a kdesi cosi. V každém případě… V
každém případě má lidstvo právo zajímat se o ty tajemné depeše, ne? Ať jsou z
pekla nebo z Martu. Je to prostě… zájem lidské společnosti. Můžete si myslet…
Nu, nejspíš, pane, nejspíš to nebudou radiodepeše o Červené karkulce. Tak.“
Pan Carson se rozběhl po světnici. „Předně je jisto,“ uvažoval nahlas, „že
dotyčná vysílací stanice… je někde ve střední Evropě, přibližně uprostřed
okruhu těch poruch, že ano? Je poměrně slabá, ježto hovoří jenom v noci.
Saprlot, tím hůř; Eiffelka nebo Nauen se najde lehko, že? Pane,“ zvolal náhle
a stanul jako přibitý, „považte, že v samém pupku Evropy existuje a chystá se
něco divného. Je to rozvětvené, má to své úřady, udržuje to tajné spojení; má
to technické prostředky nám neznámé, tajemné síly, a abyste věděl,“ zařval pan
Carson, „má to Krakatit! Tak!“
Prokop vyskočil jako blázen. „Jak-jakže?“
„Krakatit. Devět deka a pětatřicet decigramů. Všecko, co nám zbylo.“
„Co jste s ním dělali?“ rozzuřil se Prokop.
„Pokusy. Šetřili jsme s ním jako… jako s nějakou ctností. A jednoho večera –“
„Co?“
„Zmizel. I s porcelánovou pikslou.“
„Ukraden?“
„Ano.“
„A kdo – kdo –“
„Samozřejmě Marťané,“ šklebil se pan Carson. „Bohužel pozemským
prostřednictvím jednoho laboranta, který se nám ztratil. Ovšem že s
porcelánovou krabičkou.“
„Kdy se to stalo?“
„Nu, zrovna než mne poslali sem, za vámi. Vzdělaný člověk, Sasík. Ani prášek
nám nezůstal. Víte, proto jsem přijel.“
„A vy myslíte, že to přišlo do rukou těm… těm neznámým?“
Pan Carson jen frknul.
„Jak to víte?“
„Já to tvrdím. Poslyšte,“ řekl pan Carson houpaje se na krátkých nožkách,
„vypadám jako strašpytel?“
„N-ne.“
„Tak vám řeknu, že z tohohle mám strach. Na mou čest, plné kalhoty. Krakatit…
je zatracená věc; ta neznámá stanice je ještě horší; ale kdyby přišlo obojí do
jedněch rukou, pak… máúcta. Pak si pan Carson složí kufřík a pojede k
tasmanským lidojedům. Víte, já bych nerad viděl konec Evropy.“
Prokop si jen drtil ruce mezi koleny. „Kriste, kriste,“ šeptal pro sebe.
„Nu ano,“ mínil pan Carson. „Divím se jenom, víte, že dosud nevylítlo do
vzduchu… něco velikého. Může se jen stisknout kdesi jakási páka… a pár tisíc
kilometrů daleko – prásk! A je to. Nač ještě čekají?“
„To je jasné,“ ozval se Prokop zimničně. „Krakatit se nesmí dát z ruky. A
Tomeš, Tomšovi se musí zabránit…“
„Pan Tomeš,“ namítl Carson rychle, „prodá Krakatit samému ďáblu, když mu to
zaplatí. V této chvíli je pan Tomeš jedno z největších světových nebezpečí.“
„U čerta,“ mručel Prokop zoufale, „co tedy dělat?“
Pan Carson vydržel delší pauzu. „To je jasné,“ řekl konečně. „Krakatit se musí
dát z ruky.“
„Nnne! Nikdy!“
„Dát z ruky. Prostě proto, že to je… dešifrovací klíč. Nejvyšší čas, pane. U
všech všudy, dejte to, komu chcete, ale jen žádné dlouhé cavyky. Dejte to
Švýcarům nebo Svazu starých panen nebo čertově babičce; budou nad tím sedět
půl roku, než pochopí, že nejste blázen. Nebo to dejte nám. V Balttinu už
postavili takovou mašinu, víte, přijímací aparát. Představte si… nekonečně
rychlé výbuchy mikroskopických částeček Krakatitu. Zapalovačem je neznámy
proud. Jakmile jej tam někde zapnou, spustí celá věc: trrr ta ta trrr trrr ta
trrr ta ta ta. A je to. Dešifrovat, a hotovo. Jen mít Krakatit!“
„Nedám,“ dostal ze sebe Prokop pokrytý studeným potem. „Já vám nevěřím. Vy
byste… dělali Krakatit sami pro sebe.“
Pan Carson jen trhl koutkem úst. „Nu,“ řekl, „jde-li jen o to… Můžeme vám na
to svolat Svaz národů, Světovou poštovní unii, Eucharistický kongres nebo
které čerty ďábly chcete. Aby tedy měla dušička pokoj. Já jsem Dán a kašlu na
politiku. Tak. A vy dáte Krakatit do rukou mezinárodní komisi. Co je vám?“
„Já… já byl dlouho nemocen,“ omlouval se Prokop bledna smrtelně. „Není mi…
dosud… dobře. A… a… dva dny jsem nejedl.“
„Slabost,“ děl pan Carson, přisedl k němu a vzal jej kolem krku. „Přejde hned.
Pojedete do Balttinu. Velmi zdravá krajina. Pak můžete jet za panem Tomšem.
Budete mít peněz jako slupek. Budete big man. Nu?“
„Ano,“ šeptal Prokop jako malé dítě a nechal se mírně kolébat.
„Tak tak. Přílišné napětí, víte? To nic není. Hlavní… hlavní je budoucnost.
Člověče, vy jste prožil bídy, co? Jste chlapík. Vida, už je líp.“ Pan Carson
zamyšleně kouřil. „Hrozně ohromná budoucnost. Dostanete spoustu peněz. Mně
dáte deset procent, že? Je to už tak mezinárodní zvyk. Carson taky potřebuje…“
Před barákem zatroubilo auto.
„Nu sláva,“ oddychl si pan Carson, „tady je vůz. Tak, pane, jedeme.“
„Kam?“
„Zatím se najíst.“


XX.

Den nato se probudil Prokop se strašně těžkou hlavou a nemohl zprvu pochopit,
kde vlastně je; čekal, že uslyší kvokání slepic nebo hlaholné vyštěkávání
Honzíkovo. Pomalu si uvědomoval, že už není v Týnici; že leží v hotelu, kam
jej pan Carson dopravil opilého do bezvědomí, nalitého, řvoucího jako zvíře;
ale teprve když si pustil na hlavu proud studené vody, upamatoval se na celý
včerejšek a byl by se propadl hanbou.
Už při obědě pili, ale jen málo, jen tolik, že byli oba náramně rudí a vozili
se pak autem někde po sázavských či jakých lesích, aby se jim to vypařilo z
hlavy; přitom Prokop bez ustání žvanil, zatímco pan Carson žmoulal cigáro a
kýval. „Budete big man.“ Big man, big man dunělo Prokopovi v hlavě jako zvon;
hrome, kdyby mne v této slávě viděla… ta jistá se závojem! Nafukoval se před
Carsonem k prasknutí; ale ten jenom pokyvoval hlavou jako mandarín a ještě
rozdmychoval jeho zběsilou pýchu. Prokop div nevyletěl z auta samou
horečností; vykládal podle všeho, jak si představuje světový ústav
destruktivní chemie, socialismus, manželství, výchovu dětí a jiné nesmysly.
Ale večer to začalo doopravdy. Kde všude pili, to ví bůh; bylo to hrozné,
Carson platil za všechny neznámé, rudý, leskly, s kloboukem naraženým, zatímco
nějaké holky tancovaly, kdosi rozbíjel sklenice a Prokop vzlykaje zpovídal se
Carsonovi ze své strašlivé lásky k té, jíž nezná. Při této vzpomínce se Prokop
studem a bolestí chytal za hlavu.
Pak ho, křičícího „Krakatit“, vsadili do auta. Ďas ví, kam ho vezli; uháněli
po nekonečných silnicích, vedle Prokopa poskakoval rudý ohýnek, to byl asi pan
Carson se svým cigárem, a škytal „rychleji, Bobe“ či co. Najednou v jakémsi
ohybu proti nim vyjela dvě prudká světla, pár hlasů zavylo, auto sebou smýkalo
stranou a Prokop letěl hubou po trávě, čímž se vzpamatoval tak dalece, že
začal vnímat. Několik hlasů se zběsile hádalo a vyčítalo si opilství, pan
Carson strašlivě láteřil a bručel „teď musíme zpátky“, načež Prokopa jakožto
nejtíže raněného s tisícerými ohledy naložili do toho druhého auta, pan Carson
sedl k němu a jelo se zpátky, zatímco Bob zůstal u porouchaného vozu. V polou
cestě začal těžce raněný zpívat a povykovat a před Prahou pocítil novou žízeň.
Museli s ním projít ještě několik lokálů, než ho umlčeli.
S mračným znechucením studoval Prokop v zrcadle svou odřenou tvář. Z té trapné
podívané ho vyrušil vrátný hotelu, jenž mu – s patřičnými omluvami – přinášel
k vyplnění přihlašovací list. Prokop vyplnil své nacionále a doufal, že tím je
věc odbyta; ale sotva si vrátný přečetl jeho jméno a stav, oživl náramně a
prosil Prokopa, aby teď neodcházel; že prý jeden pán z ciziny si vyžádal, aby
mu hned z hotelu zatelefonovali, kdyby se tam pan inženýr Prokop snad ráčil
ubytovat. Jestli tedy pan inženýr dovolí atd. Pan inženýr byl tak rozlícen na
sebe sama, že by byl dovolil i to, aby mu uřízli krk. Sedl si tedy a čekal,
trpně odevzdán ve své bolení hlavy. Za čtvrt hodiny tu byl vrátný zas a
odevzdával navštívenku. Bylo na ní:

SIR REGINALD CARSON
Col. B. A., M. R. A., M. P., D. S. etc.
President of Marconi’s Wireless Co
LONDON

„Sem s ním,“ kázal Prokop, a v hloubi duše se nesmírně divil, proč mu chlapík
Carson neřekl už včera své ohromující hodnosti a proč dnes přichází s takovými
okolky; mimoto byl trochu zvědav, jak vypadá pan Carson po včerejší bohopusté
noci. Ale tu již vyvalil oči neuvěřitelně překvapen. Do dveří vcházel docela
neznámý pán, o dobrý loket větší než včerejší pan Carson.
„Very glad to see you,“ pronesl zvolna neznámý gentleman a poklonil se asi
tak, jako by byl telegrafní tyč. „Sir Reginald Carson,“ představil se a
ohlížel se po židli.
Prokop ze sebe vydal neurčitý zvuk a ukázal mu na židli. Gentleman pravoúhle
usedl a jal se obšírně svlékat velkolepé jelení rukavice. Byl to velmi dlouhý
a nesmírně vážný pán s koňskou tváří nažehlenou do přísných záhybů; v kravatě
ohromný indický opál, na zlatém řetízku antická kamej, ohromné nohy hráče
golfu, zkrátka každým coulem lord. Prokop tiše žasnul. „Tak prosím,“ ozval se
konečně, když už to trvalo nepřežitelně dlouho.
Gentleman neměl nijak naspěch. „Zajisté,“ začal posléze po anglicku, „zajisté
jste se podivil, když jste našel v novinách moje anonce. Předpokládám, že jste
inženýr Prokop, autor eh velmi zajímavých článků o explozívních látkách.“
Prokop mlčky přikývl.
„Velmi potěšen,“ řekl sir Carson nikterak nechvátaje. „Pátral jsem po vás v
jisté záležitosti vědecky velmi zajímavé a prakticky důležité pro naši
společnost, Marconi’s Wireless, jejímž prezidentem mám tu čest býti, neméně
než pro Mezinárodní unii pro bezdrátovou telegrafii, kterážto mi prokázala
nezaslouženou čest zvolivši mne svým generálním sekretářem. Zajisté se
divíte,“ pokračoval neudýchán tak dlouhou větou, „že tyto vážené společnosti
mne vysílají k vám, ačkoli vaše vynikající práce se pohybují na zcela jiném
poli. Dovolte.“ Na tato slova otevřel sir Carson svou krokodýlí aktovku a
vyňal nějaké papíry, blok a zlatou tužku.
„Asi po tři čtvrtě roku,“ začal pomalu a nasazoval si zlatý skřipec, aby se
podíval do svých papírů, „konstatují evropské bezdrátové stanice –“
„Promiňte,“ skočil mu do řeči Prokop nemoha se už ovládat, „tedy ty inzeráty
jste dával vy?“
„Zajisté. Tedy konstatují jisté pravidelné poruchy –“
„– v úterý a v pátek, vím. Kdo vám řekl o Krakatitu?“
„Byl bych k tomu došel sám,“ pronesl ctihodný lord poněkud káravě. „Well,
přeskočím bližší podrobnosti, předpokládaje, že jste do jisté míry informován
o našich nesnázích a o eh a –“
„– o tajné světové konspiraci, ne?“
Sir Carson vytřeštil bleděmodré oči. „Prosím za prominutí, o jaké konspiraci?“
„Nu, o těch záhadných nočních depeších, o tajemné organizaci, která je vysílá
–“
Sir Reginald Carson ho zarazil. „Fantazie,“ řekl s politováním, „čiré
fantazie. Já vím, nadhodily to dokonce Daily News, když naše společnost
vypsala onu poměrně značnou odměnu –“
„Vím,“ řekl rychle Prokop, obávaje se, aby se o ní pomalý lord nerozhovořil.
„Ano. Čirý nesmysl. Celá věc má jen obchodní pozadí. Někdo má zájem na tom,
aby dokázal nespolehlivost našich stanic, rozumíte? Chce podrýt veřejnou
důvěru. Bohužel naše receptory a – eh – koherery nemohou zjistit zvláštní druh
vln, kterými se toto rušení děje. A jelikož se nám dostalo zprávy, že jste v
držení jakési substance nebo chemikálie, která velmi, velmi pozoruhodně
reaguje na ony poruchy –“
„Od koho zprávy?“
„Od vašeho spolupracovníka, pana – eh – pana Tomese. Mister Tomes, že ano?“
Pomalý gentleman vylovil ze svých papírů nějaký dopis. „Dear Sir,“ četl s
jakousi námahou, „nalézám v novinách vypsání odměny et cetera. Jelikož se v
přítomné době nemohu vzdáliti z Balttinu, kde pracuji na jistém vynálezu, a
ježto věc tak velikého dosahu se nedá písemně vyřizovat, prosím, abyste nechal
v Praze vyhledat mého přítele a dlouholetého spolupracovníka Mr ing. Prokopa,
který má v držení nově vynalezenou látku, Krakatit, tetrargon jisté olovnaté
soli, jehož syntéza se provádí za specifických účinků vysokofrekvenčního
proudu. Krakatit reaguje, jak dokazují přesné experimenty, na neznámé rušivé
vlny silnou explozí; z čehož sám sebou plyne jeho rozhodující význam pro
výzkum řečených vln. Vzhledem k důležitosti věci předpokládám za sebe i za
svého přítele, že vypsaná odměna bude podstatně zvý-zvýšena –“ Sir Carson se
zakuckal. „To je celkem vše,“ řekl. „O odměně bychom si promluvili zvlášť.
Podepsán Mr Tomes v Balttinu.“
„Hm,“ řekl Prokop jat vážným podezřením, „že by takováhle soukromá…
nespolehlivá… fantastická zpráva stačila Marconiově společnosti –“
„Beg your pardon,“ namítal dlouhý pán, „dostalo se nám samozřejmě velmi
přesných zpráv o jistých pokusech v Balttinu –“
„Aha. Od jakéhosi saského laboranta, že?“
„Ne. Od našeho vlastního zástupce. Hned vám to přečtu.“ Sir Carson jal se
znovu lovit ve svých papírech. „Tady je to. ,Dear Sir, zdejším stanicím se
dosud nedaří překonati známé poruchy. Pokusy se zvýšenými vysílacími energiemi
selhaly naprosto. Dostalo se mi důvěrné, ale spolehlivé zprávy, že vojenský
ústav v Balttinu získal nějaké kvantum jisté látky –‘“
Zaklepáno. „Vstupte,“ řekl Prokop, a vešel sklepník s vizitkou: „Nějaký pán
prosí –“
Na vizitce stálo:

ING. CARSON, Balttin

„Ať vejde,“ kázal Prokop náhle rozjařen a naprosto nedbaje znamení protestu ze
strany sira Carsona.
Vzápětí vstoupil včerejší pan Carson s tváří velmi popleněnou nevyspáním a
hnal se k Prokopovi vydávaje zvuky radosti.


XXI.

„Počkejte,“ zarazil ho Prokop. „Dovolte, abych vás představil. Inženýr Carson,
sir Reginald Carson.“
Sir Carson sebou trhl, ale zůstal sedět s neporušenou důstojností; zato
inženýr Carson úžasem hvízdl a snesl se na židli jako člověk, kterému nohy
vypověděly službu. Prokop se opřel o dvéře a pásl se na obou pánech s
bezuzdnou zlomyslností. „Tak co?“ zeptal se konečně.
Sir Carson jal se skládati své papíry do aktovky. „Zajisté,“ řekl pomalu,
„bude lépe, navštívím-li vás jindy –“
„Jen račte zůstat,“ přerušil ho Prokop. „Prosím vás, pánové, nejste snad spolu
příbuzní?“
„Ba ne,“ ozval se inženýr Carson. „Spíš naopak.“
„Který z vás je tedy doopravdy Carson?“
Nikdo neodpověděl; bylo to trapné.
„Požádejte toho pána,“ řekl ostře sir Reginald, „aby vám ukázal své papíry.“
„Beze všeho,“ vysypal inženýr Carson, „ale až po panu předřečníkovi. Tak.“
„A kdo z vás inzeroval?“
„Já,“ prohlásil bez váhání inženýr Carson. „Můj nápad, pane. Konstatuju, že i
v našem oboru je neslýchanou špinavostí svést se zadarmo na cizím nápadu.
Tak.“
„Račte dovolit,“ obrátil se sir Reginald k Prokopovi se skutečnou mravní
nevolí, „to už je příliš. Jak by to bylo vypadalo, kdyby vycházel ještě jeden
inzerát s jiným jménem! Prostě jsem musel přijmout fakt, jak to tamten pán
udělal.“
„Aha,“ dorážel bojovně pan Carson, „a proto ten pán si přisvojil taky mé
jméno, víte?“
„Konstatuji prostě,“ ohradil se sir Reginald, „že tamten pán se naprosto
nejmenuje Carson.“
„Jak se tedy jmenuje?“ tázal se Prokop chvatně.
„… Přesně to nevím,“ vycedil opovržlivě lord.
„Carsone,“ obrátil se Prokop k inženýrovi, „a kdo je tenhle pán?“
„Konkurence,“ řekl s hořkým humorem pan Carson. „Je to ten pán, co mne
podvrženými listy chtěl vylákat na všelijaká místa. Nejspíš mne tam chtěl
seznámit s moc milými lidmi.“
„Se zdejší vojenskou policií, prosím,“ zamručel sir Reginald.
Inženýr Carson zle blýskl očima a varovně zakašlal: Prosím, o tomhle nemluvit!
sic –
„Chtějí si pánové navzájem ještě něco vysvětlit?“ šklebil se Prokop u dveří.
„Ne, nic už,“ řekl důstojně sir Reginald; doposud neuznal druhého Carsona ani
za hodna pohledu.
„Tak tedy,“ začal Prokop, „především vám děkuju za návštěvu. Za druhé mám
velikou radost, že Krakatit je v dobrých rukou, totiž v mých vlastních; neboť
kdybyste měli nejmenší naději dostat jej jinak, nebyl bych asi tak tuze
hledaná osoba, že? Za tuhle nedobrovolnou informaci jsem vám náramně vděčen.“
„Ještě nejásejte,“ bručel pan Carson. „Zbývá –“
„– on?“ řekl Prokop ukazuje na sira Reginalda.
Pan Carson zavrtěl hlavou. „Kdepak! ale neznámý třetí.“
„Odpusťte,“ mínil Prokop skoro uražen, „snad si nemyslíte, že vám něco věřím z
toho, co jste mi včera napovídal.“
Pan Carson s politováním pokrčil rameny. „Nu, jak chcete.“
„Dále a za třetí,“ pokračoval Prokop, „bych vás prosil, abyste mi řekli, kde
je teď Tomeš.“
„Ale vždyť jsem vám povídal,“ vyskočil pan Carson, „že tohle nesmím – Přijeďte
do Balttinu, a je to.“
„Tak vy, pane,“ obrátil se Prokop k siru Reginaldovi.
„Beg your pardon,“ pronesl dlouhý gentleman, „ale tohle nechám pro sebe.“
„Tedy za čtvrté vám kladu na srdce, abyste se tady navzájem nesnědli. Já zatím
půjdu –“
„– na policii,“ mínil sir Reginald. „Velmi správně.“
„Těší mne, že s tím souhlasíte. Odpusťte, že vás tu zatím zamknu.“
„Oh, prosím,“ řekl lord zdvořile, zatímco pan Carson se pokoušel zoufale
protestovat.
S velkou úlevou zamkl Prokop za sebou dvéře a ještě k nim postavil dva
sklepníky, načež běžel na blízké komisařství; neboť považoval za vhodno
poskytnout tam jakés takés vysvětlení. Ukázalo se, že věc není tak lehká;
poněvadž nemohl oba cizince nařknout aspoň z krádeže stříbrných lžiček nebo z
hraní makaa, měl velkou práci překonat pochybnosti policejního úředníka, který
ho patrně pokládal za blázna. Konečně – snad aby už měl pokoj – přidělil
Prokopovi civilního strážníka, osobnost velmi ošoupanou a mlčelivou. Když
dorazili do hotelu, našli oba sklepníky statečně vzepřené o dvéře za ohromného
shonu veškerého personálu. Prokop odemkl a civilní strážník zafrkav nosem
vstoupil klidně dovnitř, jako by si šel kupovat šle. Pokoj byl prázdný. Oba
páni Carsonové zmizeli.
Mlčelivá osobnost se jen omrkla a rovnou se ubírala ke koupelně, na kterou
Prokop dočista zapomněl. Bylo tam okno dokořán otevřené do světlíku, a v
protější straně vyražené okénko k záchodu. Mlčelivá osobnost zamířila k
záchodu. Ten ústil do jiné chodby, byl zamčen a klíč zmizel. Strážník
zakroutil v zámku paklíčem a otevřel: bylo tam prázdno, jen na sedadle klozetu
byly stopy nohou. Nemluvná osobnost vše zase zamkla a řekla, že sem pošle pana
komisaře.
Pan komisař, človíček velmi pohyblivý a slavný kriminalista, se dostavil velmi
brzo; ždímal z Prokopa dobré dvě hodiny, chtěje se mermomocí dozvědět, o čem
měl co jednat s oběma pány; zdálo se, že má velikou chuť zatknout aspoň
Prokopa, který upadal přes tu chvíli do hrozných rozporů ve vlastních
výpovědích, pokud se týkaly jeho styků s oběma cizinci. Potom vyslechl
vrátného a sklepníky a důtklivě vyzval Prokopa, aby se o šesté hodině dostavil
na policejní ředitelství; do té doby aby se raději z hotelu nehnul.
Zbytek dne strávil Prokop běhaje po pokoji a mysle s hrůzou na to, že bude asi
zavřen; neboť jaké může dát vysvětlení, když o Krakatitu pro živého boha nic
neřekne? Čertví jak dlouho může taková vyšetřovací vazba trvat; a tak místo
aby mohl hledat ji, tu neznámou v závoji… Prokop měl oči plné slz; cítil se
sláb a měkký, že se až styděl. Před šestou se však vyzbrojil vší svou
statečností a pustil se na policejní ředitelství.
Uvedli ho hned do kanceláře, kde byly tlusté koberce, kožená křesla a velká
krabice s doutníky (byla to kancelář policejního prezidenta). U psacího stolu
objevil Prokop obrovská, boxerská záda nakloněná nad papíry, záda, jež v něm
na prvý pohled budila hrůzu a pokoru. „Posaďte se, pane inženýre,“ řekla záda
přívětivě, osušila něco a obrátila se k Prokopovi tváří neméně monumentální,
vhodně umístěnou na tuří šíji. Mohutný pán vteřinku studoval Prokopa a řekl:
„Pane inženýre, nebudu vás nutit, abyste mně povídal, co z příčin jistě
uvážených hodláte nechat pro sebe. Znám vaši práci. Myslím, že v té
záležitosti šlo o nějakou vaši třaskavinu.“
„Ano.“
„Věc má asi větší význam… řekněme vojenský.“
„Ano.“
Mohutný pán se zvedl a podával Prokopovi ruku: „Chtěl jsem vám jenom, pane
inženýre, poděkovat, že jste ji neprodal zahraničním agentům.“
„To je všechno?“ vydechl Prokop.
„Ano.“
„Chytli jste je?“ vyhrkl Prokop.
„Proč?“ usmál se pán. „K tomu nemáme práva. Pokud jde o tajemství jenom vaše a
nikoliv o tajemství naší armády…“
Prokop pochytil jemnou výtku a upadl do rozpaků. „Ta věc… není dosud zralá…“
„Věřím. Spoléhám na vás,“ řekl mocný muž a znovu mu podal ruku.
To bylo vše.


XXII.

„Musím postupovat metodicky,“ umiňoval si Prokop. Dobrá, tedy po předlouhém
rozvažování a nejpodivnějších nápadech ustanovil se na tomto postupu:
Především dával obden do všech větších novin inzerát: „Pan Jiří Tomeš. Dámu v
závoji prosí doručitel s poraněnou rukou o udání adresy. Velmi důležité. P.
zn., 40 000‘ do inz. k. Grégr.“ Tato formulace se mu zdála velmi chytrá; není
sice jisto, že by mladá dáma četla noviny vůbec a inzertní část zvláště, nu
ale kdoví? Náhoda je mocná. Avšak místo náhody dostavily se okolnosti, jež
bylo možno předvídat, ale na něž Prokop předem nepomyslel. Na udanou značku
došla totiž celá spousta korespondence, jenže byly to většinou účty, upomínky,
hrozby a hrubosti na adresu nezvěstného Tomše; nebo „pan Jiří Tomeš nechť ve
vlastním zájmu udá svůj pobyt pod zn…“ a podobně. Mimoto očumoval v inzertní
kanceláři jakýsi hubený člověk, který, když Prokop si vyzvedl korespondenci, k
němu přistoupil a ptal se ho, kde bydlí pan Jiří Tomeš. Prokop si na něho
vyjel tak hrubě, jak okolnosti dovolovaly, a tu hubený pán se vytasil s
policejní legitimací a vyzval Prokopa důrazně, aby nedělal hlouposti. Šlo tu
totiž o onu jistou zpronevěru a jiné ošklivé věci. Prokopovi se podařilo
přesvědčit hubeného pána, že především on sám by nesmírně potřeboval vědět,
kde pan Tomeš je; nicméně po této příhodě a prostudování došlé korespondence
jeho důvěra v úspěch inzerce valně ochabla. Skutečně také na další anonce
docházelo odpovědí stále méně, zato však byly pořád hrozivější.
Za druhé navštívil soukromou detektivní kancelář. Vyložil tam, že hledá
neznámou dívku v závoji, a pokoušel se ji popsat. Byli ochotni opatřit mu o ní
diskrétní informace, udá-li buď její bydliště, nebo její jméno. I nezbylo mu
než odejít s nepořízenou.
Za třetí dostal geniální nápad. V řečené obálce, která ho neopouštěla ve dne
ani v noci, bylo – krom menších bankovek – třicet tisícovek opatřených páskou,
jak je v bankách zvykem při vyplácení větších peněz. Nebylo tam jméno banky;
ale aspoň to je nejvýš pravděpodobno, že dívka je vybrala v některém peněžním
ústavě téhož dne, kdy on, Prokop, s nimi odejel do Týnice. Nuže, nyní jen
vědět přesné datum, a pak stačí obejít všechny banky v Praze a vyprosit si,
aby mu udali jméno osoby, která toho dne vyzvedla třicet tisíc nebo o něco
více korun. Ano, vědět přesné datum; Prokop si byl sice neurčitě vědom, že
Krakatit mu vybuchl v úterý nebo kdy (dva dny předtím byla neděle či svátek),
takže dívka vyzvedla peníze pravděpodobně kterési středy; avšak týdnem ani
měsícem si nebyl Prokop jist, mohlo to být v březnu nebo v únoru. Se strašnou
námahou hleděl se upomenout nebo si vypočítat, kdy to asi bylo; avšak každý
kalkul se zastavil u toho, že nevěděl, jak dlouho stonal. Dobrá, jistě však
vědí u Tomšů v Týnici, kterého týdne jsem k nim vpadl! Oslněn touto nadějí
depešoval starému doktoru Tomši: „Telegrafujte datum, kdy jsem k vám přijel.
Prokop.“ Sotva depeši odeslal, zamrzelo ho to; neboť cítil zrovna palčivě, že
se k nim nezachoval pěkně. Skutečně také odpověď nedocházela. Když už chtěl
tuto nitku pustit z ruky, napadlo ho, že snad si na onen den vzpomene
domovnice od Jirky Tomše. Letěl k ní; avšak domovnice tvrdila, že to bylo
někdy v sobotu. Prokop si zoufal; ale tu ho došel dopis napsaný velkými a
pečlivými písmenami vzorné školačky, že do Týnice přijel dne toho a toho, ale
„tati nesmí vědět, že jsem Vám psala“. Nic víc. Podepsána Anči. V Prokopovi
bůhvíproč krvácelo srdce nad těmi dvěma řádky.
Nuže, se šťastně získaným datem běžel do první banky: mohou-li mu říci, kdo si
toho a toho dne vybral tady v ústavě řekněme třicet tisíc korun. Kroutili nad
tím hlavou, že prý to není zvykem ani vůbec přípustno; ale když viděli, jak je
zdrcen, šli se s někým poradit a pak se ho ptali, na jaký účet byly peníze
vybrány; nebo aspoň zda byly vyzvednuty na knížku, na běžný účet, na šek či
akreditiv. Prokop to ovšem nevěděl. Dále, pravili mu, snad ten dotyčný tu
prodal jen cenné papíry; pak jeho jméno ani nemusí být v knihách. A když jim
posléze Prokop doznal, že naprosto neví, zda ty peníze byly vyplaceny v téhle
bance nebo v kterékoliv jiné, dali se mu do smíchu a ptali se, chce-li s
takovýmto dotazem zběhat všech dvě stě padesát či kolik peněžních ústavů,
filiálek a směnáren v Praze. Tak Prokopův geniální nápad selhal naprosto.
Zbývala už jen čtvrtá možnost, totiž náhoda, že ji potká. I do té náhody se
pokoušel Prokop vpravit jakousi metodu; rozdělil si plán Prahy na sektory a
propátrával jeden úsek po druhém běhaje od rána do večera. Jednoho dne
spočítal, s kolika lidmi se takto za celý den setká, a došel k číslu skoro
čtyřiceti tisíc; tedy vzhledem k úhrnnému počtu obyvatelstva veškeré Prahy je
tu pravděpodobnost asi jedna ku dvanácti, že uvidí tu, již hledá. Ale i tato
malá pravděpodobnost je velkou nadějí. Jsou ulice a místa, která se už předem
zdají nad jiné hodna toho, aby ona tu bydlela nebo tudy prošla; ulice s akáty
kvetoucími, důstojná stará náměstí, intimní kouty hlubokého a vážného života.
Rozhodně není možno, že by přebývala v této hlučné a ponuré ulici, kudy se
jenom spěchá; ani v pravoúhlé suchosti těch činžáků bez tváře, ani ve rmutné
špíně staroby; proč by však nemohla žít zrovna za těmihle velkými okny, za
nimiž tají dech stinné a jemné ticho? Divě se, bloudě jako v snách objevoval
Prokop poprvé v životě, co vše je v tomto městě, kde strávil tolik let; bože,
tolik krásných míst, kde uplývá život pokojný a zralý a vábí tě, člověče
roztěkaný: ohranič, ohranič sebe sama.
Bezpočtukráte hnal se Prokop za mladými ženami, jež mu z dálky bůhvíčím
připomněly tu, již viděl jen dvakrát; běžel za nimi s divě tlukoucím srdcem:
což kdyby to byla ona! A kdo nám poví, jaká to byla divinace nebo čich: vždy
to byly ženy neznámé sice, ale krásné a smutné, uzavřené v sebe a zaštítěné
nevímjakou nedostupností. Jednou pak už si byl skoro jist, že to je ona; hrdlo
se mu sevřelo tak, že musel stanout, aby vydechl; tu ta dotyčná vstoupila do
tramvaje a ujela. Po tři dny potom hlídkoval u oné stanice, ale už jí
neuviděl.
Nejhorší pak byly večery, kdy na smrt unaven tiskl ruce mezi koleny a namáhal
se zkombinovat nějaký nový detektivní plán. Bože, nikdy se nevzdám toho,
nalézt ji; jsem posedly, budiž; jsem blázen, blbec a maniak; ale nikdy se toho
nevzdám. Čím víc mi uniká, tím je to silnější; prostě… je to… osud či co.
Jednou se probudil uprostřed noci, a bylo mu náhle neodvratně jasno, že takhle
jí jakživ nenajde; že musí vstát a vyhledat Jirku Tomše, který o ní ví a musí
mu o ní říci. I ustrojil se uprostřed noci a nemohl se dočkat rána. Nebyl
připraven na nepochopitelné potíže a průtahy s opatřením pasu; nerozuměl ani,
co všechno na něm chtějí, a zuřil i tesknil horečnou netrpělivostí. Konečně,
konečně jedné noci ho nesl rychlík za hranice. A tedy nejprve do Balttinu!
Teď se to rozhodne, cítil Prokop.


XXIII.

Rozhodlo se to bohužel jinak, než mínil.
Měl totiž plán vyhledat v Balttinu toho jistého, co se mu vydával za Carsona,
a říci mu asi tolik: něco za něco, já vám kašlu na peníze; vy mne dovedete
ihned k Jiřímu Tomši, se kterým mám co jednat, a za to dostanete dobrou
třaskavinu, dejme tomu fulminát jodu se zaručenou detonací nějakých jedenácti
tisíc sekundometrů, nebo pro mne a za mne ten jistý kovový azid s celými
třinácti tisíci, pane, a dělejte si s tím, co chcete. – Byli by ovšem blázni,
kdyby na takový obchod nepřistoupili.
Továrna v Balttinu se mu zdála zvenčí nehrubě veliká; trochu v něm hrklo, když
místo na portýra narazil na vojenskou hlídku. Ptal se jí na pana Carsona (– u
čerta, vždyť se ten člověk tak nejmenuje!); ale vojáček neřekl a ani b a s
bajonetem ho vedl k šikovateli. Ten neřekl o mnoho víc a dovedl Prokopa k
důstojníkovi. Inženýr Carson je tu neznám, řekl důstojník, a co prý s ním pán
chce? Prokop prohlásil, že chce vlastně mluvit s panem Tomšem. To na
důstojníka mělo tak dalece účinek, že poslal pro pana obrsta. Pan obrst, velmi
tlustý astmatický člověk, jal se Prokopa důtklivě vyslýchat, kdo je a co tu
chce; to už bylo v kanceláři asi pět vojenských pánů a prohlíželi si Prokopa
tak, až se potil. Bylo zřejmo, že čekají na někoho, pro koho zatím
telefonovali. Když se ten někdo přihnal jako vítr, ukázalo se, že je to pan
Carson; titulovali ho direktorem, ale jeho skutečné jméno Prokop nezvěděl
nikdy. Křičel radostí, když spatřil Prokopa, a tvrdil, že na něho už čekali a
kdesi cosi; hned nařídil telefonovat „do zámku“, aby připravili „kavalírské“
hostinské pokoje, chytil Prokopa pod paží a vedl jej balttinským závodem.
Ukázalo se, že to, co Prokop považoval za továrnu, je jenom vojenská a
hasičská ubikace u vchodu; odtud vede dlouhá chaussée tunelem v porostlé, asi
deset metrů vysoké hrázi. Pan Carson uvedl Prokopa nahoru, a teprve nyní si
Prokop jakžtakž uvědomil, co to jsou balttinské závody: celé město muničních
baráků označených číslicemi a písmenami, kopečky pokryté trávou, což prý jsou
sklady, o kus dál nádražní park s rampami a jeřáby a za ním nějaké docela
černé budovy a prkenné boudy. „Vidíte tamten les?“ ukazoval pan Carson k
obzoru. „Za ním jsou teprve ty pokusné laboratoře, víte? A tamhle, co jsou ty
pískové vršky, je střelnice. Tak. A tadyhle v parku je zámek. Budete mrkat, až
vám ukážu laboratoře. Ef ef, to nejmodernější. A teď půjdeme do zámku.“
Pan Carson vesele žvanil, ale nic o tom, co bylo nebo má být; šli zrovna
parkem, i ukazoval mu tuhle vzácný druh Amorphophallus a tamhle jakousi
japonskou varietu třešničky; a tady už je balttinský zámek celý zarostlý
břečťanem. U vchodu čeká tichý a jemňoučký stařík v bílých rukavicích, jménem
Paul, a vede Prokopa rovnou do „kavalírského pokoje“. Jakživ nebyl Prokop v
něčem podobném; vykládané parkety, anglický empir, vše staroučké a drahocenné,
že se bál na to sednout. A než se mohl opláchnout, už je tu Paul s vajíčky,
lahví vína a třesoucí se sklenicí a staví vše na stůl tak něžně, jako by
posluhoval princezničce. Pod okny je dvůr vysypaný plavým pískem; štolba v
ohrnutých holinkách tam na dlouhé oprati lonžíruje vysokého grošovaného koně;
vedle něho stojí hubená hnědá dívka, přimhouřenýma očima sleduje koňův cval a
dává krátce jakési rozkazy, načež přiklekne a ohmatává koňovy kotníky.
Pan Carson se přižene zase jako vítr, a že prý teď Prokopa musí představit
generálnímu řediteli. Vede ho dlouhou bílou chodbou ověšenou samými parohy a
vroubenou černými vyřezávanými židlemi. Růžový panák s bílými rukavicemi
otevře před nimi dveře, pan Carson všoupne Prokopa dovnitř, do jakéhosi
rytířského sálu, a dveře se zavrou. U psacího stolu stojí vysoký stařec,
podivně vzpřímen, jako by ho právě vytáhli ze skříně a připravili k uvítání.
„Pan inženýr Prokop, Jasnosti,“ řekl pan Carson. „Kníže Hagen-Balttin.“
Prokop se zamračil a trhl zlobně hlavou; patrně tento pohyb považoval za
poklonu.
„Buďte – nám – vítán,“ pronesl kníže Hagen a podal mu předlouhou ruku. Prokop
znovu trhl hlavou.
„Dou-fám, že – budete – u nás – spokojen,“ pokračoval kníže, a Prokop si
všiml, že je na půli těla ochrnut.
„Račte – nás poctít – u stolu,“ mluvil kníže s patrnou úzkostí, aby mu
nevypadl umělý chrup.
Prokop nervózně přešlapoval. „Račte odpustit, kníže,“ začal konečně, „ale já
se tu nemohu zdržet; já – já musím ještě dnes –“
„Nemožno, naprosto nemožno,“ vyhrkl pan Carson vzadu.
„Ještě dnes se musím poroučet,“ opakoval Prokop tvrdohlavě. „Chtěl jsem jenom…
poprosit, abyste mi řekli, kde je Tomeš. Byl bych… eventuelně ochoten
poskytnout za to… eventuelně…“
„Jak?“ zvolal kníže a vytřeštil na pana Carsona oči v absolutní nechápavosti.
„Co – chce?“
„Nechte to zatím,“ zahučel pan Carson Prokopovi do ucha. „Pan Prokop jenom
míní, Jasnosti, že nebyl na vaše pozvání připraven. To nevadí,“ obrátil se
čile k Prokopovi. „Já jsem to zařídil. Dnes bude déjeuner na trávníku, tedy…
žádné černé šaty; můžete jít jak jste. Telegrafoval jsem pro krejčího; žádné
starosti, pane. Zítra je to v pořádku. Tak.“
Nyní zas Prokop vytřeštil oči. „Jaký krejčí? Co to znamená?“
„Bude – nám – zvláštní ctí,“ zakončil kníže a podal Prokopovi umrlčí prsty.
„Co to znamená?“ zuřil Prokop na chodbě a chytil Carsona za rameno. „Člověče,
teď mluvte, nebo –“
Pan Carson se rozřehtal a vysmekl se mu jako uličník. „Nebo, jaképak nebo?“
smál se prchaje a poskakuje jako míč. „Jestli mne chytíte, řeknu vám všechno.
Na mou čest.“
„Vy kašpare,“ zahromoval Prokop rozzuřen a pustil se za ním. Pan Carson
řehtaje se letěl po schodech a vyklouzl podle plechových rytířů do parku; tam
panáčkoval na trávníku dělaje si zřejmě z Prokopa tatrmany. „Tak co,“ křičel,
„co mně uděláte?“
„Zmlátím vás,“ soptil Prokop řítě se na něho celou svou těžkou vahou. Carson
kvičel radostí a skákal po trávníku kličkuje jako zajíc. „Honem,“ jásal, „tady
jsem,“ a už zase uklouzl Prokopovi pod rukou a dělal na něho kukuč za pněm
stromu.
Prokop mlčky uháněl za ním s pěstmi zaťatými, vážný a hrozný jako Aiás. Supěl
už bez dechu, když najednou zahlédl, že ze zámeckých schodů přihmouřenýma
očima sleduje jejich běh hnědá amazonka. Zastyděl se nesmírně, stanul a jaksi
se lekl, že teď snad ta dívka mu přijde ohmatat kotníky.
Pan Carson, najednou zase docela vážný, coural se k němu s rukama v kapsách a
povídal přátelsky: „Málo tréninku. Nesmíte pořád sedět. Cvičit srdce. Tak.
Aá,“ zahlaholil rozzářen, „naše velitelka, haholihoo! Dcera starého,“ dodával
tiše. „Princezna Wille, totiž Wilhelmina Adelhaida Maud a tak dále. Zajímavá
holka, osmadvacet let, ohromná jezdkyně. Musím vás představit,“ řekl nahlas a
vlekl vzpouzejícího se Prokopa k dívce. „Nejjasnější princezno,“ volal zdálky,
„tady vám – do jisté míry proti jeho vůli – představuji našeho hosta. Inženýr
Prokop. Strašně zuřivý člověk. Chce mne zabít.“
„Dobrý den,“ řekla princezna a obrátila se k panu Carsonovi: „Víte, že
Whirlwind má zpuchlý kotník?“
„I proboha,“ děsil se pan Carson. „Ubohá princezno!“
„Hrajete tenis?“
Prokop se mračil a nevěděl ani, že tohle platilo jemu.
„Nehraje,“ odpověděl Carson za něho a šťouchl ho do žeber. „To musíte hrát.
Princezna prohrála s Lenglenovou jen o jeden set, že?“
„Protože jsem hrála proti slunci,“ namítla princezna poněkud dotčena. „Co
hrajete?“
Prokop zas nevěděl, že to patřilo jemu.
„Pan inženýr je učenec,“ spustil Carson horlivě. „Našel atomové výbuchy a
takové věci. Ohromný duch, vážně. My jsme proti němu kuchyňské ficky. Takhle
strouhat brambory a podobně. Ale tuhle on,“ a pan Carson podivem hvízdl.
„Jednoduše kouzelník. Jestli chcete, vyrazí z vizmutu vodík. Tak, panečku.“
Šedivé oči štěrbinou sklouzly po Prokopovi, jenž tu stál zrovna uvařen v
rozpacích a vztekaje se na Carsona.
„Velmi zajímavé,“ řekla princezna a už zas se dívala jinam. „Řekněte mu, aby
mne někdy poučil. Tedy v poledne na shledanou, že ano?“
Prokop se uklonil téměř včas, a pan Carson jej vlekl do parku. „Rasa,“ povídal
uznale. „Ta ženská má rasu. Pyšná, co? Počkejte, až ji poznáte blíž.“
Prokop se zastavil. „Poslyšte, Carsone, abyste si to nepletl. Nikoho nemíním
poznat blíž. Dnes nebo zítra odjedu, rozumíte?“
Pan Carson kousal nějaký list, jakoby nic. „Škoda,“ řekl. „Tady je moc pěkně.
Nu co dělat.“
„Zkrátka mi povězte, kde je Tomeš –“
„Až pojedete odtud. Jak se vám líbil starý?“
„Co mi je po něm,“ hučel Prokop.
„Nu ano. Antikní kus, pro reprezentaci. Bohužel ho pravidelně jednou týdně
raní mrtvice. Ale Wille je báječné děvče. Pak je tu Egon, klacek, osmnáct let.
Oba sirotci. Potom hosti, nějaký bratránek princ Suwalski, všelijací oficíři,
Rohlauf, von Graun, víte, Jockey Club, a doktor Krafft, vychovatel, a taková
společnost. Dnes večer musíte přijít mezi nás. Pivní večer, žádná šlechta,
naši inženýři a podobně, víte? Tamhle v mé vile. Je to na vaši počest.“
„Carsone,“ řekl Prokop přísně. „Chci s vámi vážně mluvit než odjedu.“
„To nespěchá. Odpočněte si, a je to. Nu, já musím po své práci. Můžete dělat,
co vám je libo. Žádné formality. Chcete-li se vykoupat, tak tamhle je rybník.
Nic nic, až později. Udělejte si pohodlí. Tak.“
A byl pryč.


XXIV.

Prokop se coural po parku mrze se na cosi a nevyspale zívaje. Divil se, co s
ním vlastně chtějí, a měřil s nelibostí své boty podobné vojenským bagančatům
i své unošené nohavice. Ponořen v tyto myšlenky divže nevlezl až doprostřed
tenisového hříště, kde princezna hrála se dvěma panáky v bílých šatech. Uhnul
rychle a pustil se směrem, kde předpokládal konec parku. Nu, na této straně
končil se pak park jakousi terasou: kamenná balustrádka a kolmo dolů zeď
nějakých dvanáct metrů vysoká. Možno se pokochat vyhlídkou na borové lesíky a
na vojáčka, který dole přechází s nasazeným bajonetem.
Prokop zamířil v tu stranu, kde se park svažoval dolů; našel tam rybník s
koupelnami, ale přemáhaje chuť vykoupat se vešel do pěkného březového hájku.
Tak vida, tady je jenom laťový plot a u polozarostlé cestičky vrátka; nejsou
dokonce zavřena a lze vyjít ven, do borového lesa. Putoval tiše po klouzavém
jehličí až na kraj lesa. A sakra, tady je dobré čtyři metry vysoký plot z
ostnatého drátu. Jakpak, řekněme, je takový drát pevný? Zkoušel to opatrně
rukou i nohou, až shledal, že k jeho počínání se zájmem přihlíží vojáček s
bajonetem na druhé straně plotu.
„To je horko, že?“ řekl Prokop, aby to zamluvil.
„Tudy se nesmí,“ povídal vojáček; i otočil se Prokop na patě a ubíral se podle
ostnatého plotu dál. Borový les přešel v mlází, a za ním jsou nějaké kůlny a
chlévy, patrně panský dvůr; nakoukl tam plotem, a tu se uvnitř rozlehl strašný
řev, chroptění a štěkot, a dobrý tucet dog, bloodhoundů a vlčáků se sápal na
plot. Čtyři páry nedůvěřivých očí vykoukly z čtverých dveří. Prokop pro
jistotu pozdravil a chtěl jít dál; ale jeden čeledín vyběhl za ním a povídal,
že „tudy se nesmí“, načež ho dovedl zpátky až k vrátkům do březového háje.
To vše Prokopa velmi rozlaďovalo. Carson mně musí říci, kudy se jde ven,
umiňoval si; nejsem přece kanár, aby mne drželi v kleci. Vyhnul se obloukem
tenisovému hříšti a zamířil k parkové cestě, kudy ho Carson vedl nahoru do
zámku. Jenže teď se mu postavil do cesty filmový chlapík v placaté čepici, a
kam prý pán ráčí.
„Ven,“ řekl Prokop zkrátka. Ale „tudy se nesmí,“ vysvětloval mu člověk v
čepici; tudy prý se jde k rnuničním barákům, a kdo tam chce jít, musí mít
laissez-passer od ředitelství. Ostatně vrata ze zámku přímo ven jsou tuhle
zpátky, po hlavní cestě a vlevo prosím.
Prokop se tedy pustil po hlavní cestě a vlevo prosím, až přišel k velikým
mřížovým vratům. Děda vrátný mu šel otevřít. „Račte mít lístek?“
„Jaký lístek?“
„Pasírku.“
„Jakou pasírku?“
„Lístek, že smíte ven.“
Prokop se rozlítil. „Copak jsem v kriminále?“
Děda krčil lítostivě rameny: „Prosím, dneska mi dali rozkaz.“
Chudáku, myslel si Prokop, ty bys tak někoho mohl zadržet! Jen udělat rukou
takhle –
Z okna vrátného domku vyhlédla povědomá tvář, náramně podobná jistému Bobovi.
Prokop tedy ani nedokončil svou myšlenku, otočil se na patě a loudal se zpátky
k zámku. U všech rohatých, řekl si, to jsou divné okolky; skoro to vypadá,
jako by tu někdo měl být zavřen. Dobrá, promluvím si o tom s Carsonem.
Především vůbec nestojím o jejich pohostinství a nepůjdu k obědu; nebudu sedět
s panáky, kteří se tam na tenisovém hříšti chechtali za mými zády. – Rozhořčen
nesmírně odebral se Prokop do pokojů, které mu byly vykázány, a vrhl se na
staroučkou chaise longue, až to zapraskalo, a hněval se. Za chvíli zaklepal
pan Paul a ptal se vlídně a starostlivě, půjde-li pán k déjeuner.
„Nepůjdu,“ vrčel Prokop.
Pan Paul se poklonil a zmizel. Za chvilku tu byl zas a strkal před sebou stůl
na kolečkách, pokrytý sklenicemi, křehoučkým porcelánem a stříbrem. „Prosím,
jaké víno?“ ptal se něžně. Prokop zamručel cosi jako aby mu dali pokoj.
Pan Paul šel po špičkách ke dveřím a vzal tam z dvou bílých pracek velikou
mísu. „Consommé de tortues,“ šeptal pozorně a naléval Prokopovi, načež mísa
opět zmizela v bílých tlapách. Toutéž cestou přišla ryba, pečeně, saláty,
věci, které Prokop jakživ nejedl, a ani dobře nevěděl, jak se jedí; než
ostýchal se dát před panem Paulem najevo jakékoliv rozpaky. Kupodivu, jeho
hněv se nějak rozplýval. „Sedněte si,“ kázal Paulovi, ochutnávaje nosem a
patrem nahořklé bleďoučké víno. Pan Paul se šetrně uklonil a zůstal ovšem
stát.
„Poslyšte, Paul,“ pokračoval Prokop, „myslíte, že tu jsem zavřen?“
Pan Paul pokrčil uctivě rameny: „Prosím, nemohu vědět.“
„Kudy se odtud dostanu ven?“
Pan Paul chvilinku přemýšlel. „Prosím, po hlavní cestě a pak nalevo. Poroučí
milostpán kávu?“
„No třeba.“ Prokop si opařil krk skvostným moka, zatímco pan Paul mu podával
všechny vůně Arábie v cigárové krabici a stříbrný hořák. „Poslyšte, Paul,“
začal zas Prokop kousaje špičku doutníku, „děkuju vám. Neznal jste tady
nějakého Tomše?“
Pan Paul obrátil oči k nebi samým usilovným vzpomínáním. „Neznal, prosím.“
„Kolik je tu vojáků?“
Pan Paul uvažoval a počítal. „Na hlavní stráži asi dvě stě. To je infanterie.
Potom polní četníci, to nevím kolik. V Balttin-Dortum škardona husarů. Na
střelnici v Balttin-Dikkeln kanonýři, to se mění.“
„Proč tu jsou polní četníci?“
„Prosím, tady je válečný stav. Kvůli muniční továrně.“
„Aha. A to se hlídá jen tady kolem?“
„Tady jsou jen patroly, prosím. Řetěz je až dál, za lesem.“
„Jaký řetěz?“
„Hlídková zóna, prosím. Tam nikdo nesmí.“
„A kdyby chtěl člověk odejet –“
„To musí mít povolení od staničního komanda. Přeje si pán ještě něco?“
„Ne, děkuju vám.“
Prokop se natáhl na chaise longue rozkošnicky jako sytý bej. Nu uvidíme, řekl
si; až potud to není tak zlé. Chtěl vše uvážit, ale místo toho si vzpomněl,
jak před ním Carson poskakoval. Že bych ho nedohonil? napadlo ho a pustil se
za ním. Stačil jediný pětimetrový skok; ale tu se Carson vznesl jako kobylka a
hladce přelétl přes skupinu keřů. Prokop dupnul nohou a vzlétl za ním, a jen
zvedl nohy, když letěl nad vrcholky křoví. Nový odraz, a již letěl bůhvíkam,
nestaraje se dále o Carsona. Vznášel se mezi stromy, lehýnký a volný jako
pták; zkusil několik plaveckých rázů nohama, a vida, stoupal výš. To se mu
nesmírně zalíbilo. Mocnými tempy se šrouboval kolmo nahoru. Pod nohama se mu
jako pěkně narýsovaný plán otevíral zámecký park se svými altány, trávníky a
vinutými cestami; lze rozeznat tenisové hříště, rybník, střechu zámku, březový
hájek; tam je ten dvůr se psy a borový les a ostnatý plot, a napravo už
začínají muniční baráky, a za nimi vysoká zeď. Prokop zamířil vzduchem nad tu
stranu parku, kde dosud nebyl. Cestou zjistil, že to, co považoval za terasu,
je vlastně bývalé opevnění zámku, mohutná bašta s podsebitím a příkopem,
druhdy patrně napájeným z rybníka. Hlavně mu šlo o tu část parku mezi hlavním
východem a baštou; jsou tam zarostlé cestičky a divoké křoví, hradba vysoká už
jen asi tři metry a pod ní jakési smetiště nebo kompost; dále zelinářská
zahrada a kolem řádková zeď hodně chatrná a v ní zelená vrátka; za vrátky
silnice. Tam se podívám, řekl si Prokop a snášel se pomalu dolů. Tu však
vyrazila na silnici škadrona jízdy s tasenými šavlemi, a rovnou na něho.
Prokop přitáhl nohy až k bradě, aby mu je neusekli; ale tím dostal takový
kolmý rozmach, že vylétl do výše jako šíp. Když se zas podíval dolů, viděl vše
maličké jako na mapě; dole na silnici předjíždí malinká baterie děl, lesklá
hlaveň se obrací nahoru, vyrazil bílý obláček, a bum! první granát přeletěl
Prokopovi nad hlavou. Zastřelují se, mínil Prokop, a rychle vesloval rukama,
aby se dostal dál. Bum! druhý granát zafičel Prokopovi před nosem. Prokop se
dal na ústup tak rychle, jak to šlo. Bum! třetí rána mu rázem přerazila křídla
a Prokop se řítil hlavou k zemi, a procitl. Někdo klepal na dveře.
„Vstupte,“ křikl Prokop a vyskočil, nechápaje, kde vůbec je.
Vešel bělovlasý, ušlechtilý pán v černém a hluboce se uklonil.
Prokop zůstal stát a čekal, až ho vznešený pán osloví.
„Drehbein,“ řekl ministr (nejméně!) a znovu se uklonil.
Prokop se uklonil stejně hluboko. „Prokop,“ představil se. „Čím mohu sloužit?“
„Kdybyste ráčil chvilku stát.“
„Prosím,“ vydechl Prokop, žasna, co se s ním bude dít.
Bělovlasý pán studoval Prokopa s přimhouřenýma očima; dokonce si ho obešel a
pohřížil se do pozorování jeho zad.
„Kdybyste se ráčil trochu narovnat.“
Prokop se vztyčil jako voják; co to u všech všudy –
„Račte dovolit,“ řekl pán a poklekl před Prokopem.
„Co chcete?“ vyhrkl Prokop couvaje.
„Vzít míru.“ A už vytáhl ze šosu svinutý metr a jal se měřit Prokopovu
nohavici.
Prokop ustoupil až k oknu. „Nechte toho, ano?“ spustil podrážděně. „Já jsem s
žádné šaty neobjednal.“
„Už jsem dostal rozkazy,“ podotkl pán uctivě.
„Poslyšte,“ řekl Prokop přemáhaje se, „jděte mi ke všem – – Nechci žádné šaty
a dost! Rozuměl jste?“
„Prosím,“ souhlasil pan Drehbein, dřepl před Prokopem, nadzvedl mu vestu a
zatahal za dolejší kraj kalhot. „O dva centimetry víc,“ poznamenal vstávaje.
„Račte dovolit.“ Přitom mu znalecky zajel rukou do podpaží. „Příliš volné.“
„To je dobře,“ zabručel Prokop a obrátil se k němu zády.
„Děkuju,“ mínil pán a uhlazoval mu na zádech nějaký záhyb.
Prokop se rozlíceně otočil. „Člověče, ruce pryč, nebo –“
„Promiňte,“ omlouval se pán a objal ho měkce kolem pasu; a než ho Prokop mohl
přinejmenším skolit, stáhl mu pásek u vesty, ustoupil a s nakloněnou hlavou
zpytoval Prokopovu tailli. „Tak je to,“ podotkl zcela uspokojen a hluboce se
uklonil. „Mám tu čest se vám poroučet.“
„Jdi ke všem kozlům,“ křičel za ním Prokop, a „však už tu zítra nebudu,“
zakončil pro sebe, načež popuzen měřil pokoj z rohu do rohu. Hrom do toho,
copak si ti lidé myslí, že tu zůstanu půl roku?
Tu zaklepal a vešel pan Carson s tváří neviňátka. Prokop se zastavil s rukama
za zády a měřil ho ponurýma očima. „Člověče,“ řekl ostře, „kdo vlastně jste?“
Pan Carson ani nemrkl, zkřížil ruce na prsou a klaněl se jako Turek. „Princi
Alaaddine,“ pravil, „jsem džin, tvůj otrok. Kaž, a vyplním každé tvé přání.
Ráčil jste spinkat, že? Nu, blahorodí, jak se vám tu líbí?“
„Ohromně,“ mínil Prokop hořce. „Jen bych rád věděl, jsem-li tady zavřen, a
jakým právem.“
„Zavřen?“ žasl pan Carson, „propána, copak vás někdo nechtěl pustit do parku?“
„Ne, ale z parku ven.“
Pan Carson potřásl účastně hlavou: „Nemilé, že? To mne hrozně mrzí, že jste
nespokojen. Koupal jste se v rybníce?“
„Ne. Kudy se dostanu ven?“
„Božínku, hlavním vchodem. Jdete rovně a potom vlevo –“
„A tam ukážete pasírku, ne? Jenže já žádné nemám.“
„To je škoda,“ mínil pan Carson. „Tady je moc hezké okolí.“
„Hlavně moc hlídané.“
„Moc hlídané,“ souhlasil pan Carson. „Výborně řečeno.“
„Poslyšte,“ vybuchl Prokop, a čelo mu nabíhalo hněvem, „myslíte, že je
příjemné narazit každým desátým krokem na bajonet nebo ostnatý plot?“
„Kde to?“ divil se pan Carson.
„Všude v pomezí parku.“
„A co vás čerti nesou do pomezí parku? Můžete chodit uvnitř, a je to.“
„Tedy jsem zavřen?“
„Bůh uchovej! Abych nezapomněl, tady je pro vás legitimace. Laissez-passer do
závodu, víte? Kdybyste se tam náhodou chtěl podívat.“
Prokop vzal do rukou legitimaci a podivil se; byla na ní fotografie vzatá
patrně téhož dne. „A s tímhle se mohu dostat ven?“
„To ne,“ řekl honem pan Carson. „To bych vám neradil. Vůbec, dejte si na sebe
trochu pozor, co? Rozumíte? Pojďte se podívat,“ řekl od okna.
„Co je?“
„Egon se učí boxovat. Heč, dostal ji! To je von Graun, víte? Haha, ten kluk má
kuráž!“
Prokop se s odporem díval na dvůr, kde polonahý chlapec, krváceje z úst i
nosu, vzlykaje bolestí a vztekem se vrhal znovu a znovu na staršího odpůrce,
aby za okamžik odletěl zkrvácenější a bědnější než předtím. Co se mu obzvláště
příčilo, bylo, že k tomu přihlížel starý kníže ve vozíčkovém křesle, směje se
z plna hrdla, i princezna Wille bavící se přitom klidně se skvělým krasavcem.
Konečně Egon padl do písku úplně zpitomělý a nechal si odkapávati krev z nosu.
„Dobytek,“ zahučel Prokop na neznámou adresu a zatínal pěstě.
„Tady nesmíte být tak citlivý,“ prohlásil pan Carson. „Tvrdá kázeň. Život…
jako na vojně. Nemazlíme se s nikým,“ pointoval tak důrazně, že to vypadalo
jako hrozba.
„Carsone,“ řekl Prokop vážně, „jsem tedy… jaksi… ve vězení?“
„Ale kdepak! Jste jenom v střeženém podniku. V prachárně to není jako u
holiče, co? Tomu se musíte přizpůsobit.“
„Zítra odjedu,“ vyrazil Prokop.
„Haha,“ smál se pan Carson a třepl ho po břiše. „Báječný šprýmař! Tedy
přijdete dnes večer mezi nás, že?“
„Nikam nepřijdu! Kde je Tomeš?“
„Co? Aha, váš Tomeš. Nu, zatím tuze daleko. Tohle je klíč od vaší laboratoře.
Nikdo vás tam nebude rušit. Škoda že nemám pokdy.“
„Carsone,“ chtěl ho zadržet Prokop, ale zarazil se před posuňkem tak
velitelským, že se neodvážil ničeho dál; a pan Carson vyklouzl ven hvízdaje si
jako cvičený špaček.
Prokop se svou legitimací se pustil k hlavním vratům. Děda vrátný ji studoval
a vrtěl hlavou; tenhle lístek prý platí jen pro východ C, tamhle, co se jde k
laboratořím. Prokop putoval k východu C; filmový chlapík s placatou čepicí
prohlédl legitimaci a ukazoval: tady rovně, pak třetí příčná severní cesta.
Prokop se ovšem dal první cestou k jihu; ale po pěti krocích ho zadržel polní
četník: zpátky a třetí cesta vlevo. Prokop si odplivl na třetí cestu vlevo a
nabral to rovnou přes louku: za okamžik ho honili tři lidé, tudy že se nesmí.
Šel tedy poslušně třetí severní cestou, a když si myslel, že už na něho
nekoukají, zamířil mezi muniční sklady. Tam ho sebral voják s bajonetem a
poučil ho, že má jít tamhle, na rozcestí VII, cesta N 6. Prokop zkoušel své
štěstí na každé křižovatce; všude ho zadrželi a posílali na cestu VII, N 6; i
umoudřil se konečně a pochopil, že legitimace popsaná písmenami „C 3 n. w. F.
H. A. VII, N 6. Bar. V, 7. S. b.!“ má smysl tajemný a nevyhnutelný, jemuž jest
se slepě podříditi. Šel tedy, kam mu ukazovali cestu. Tady už nebyly muniční
baráky, ale malé betonové stavbičky všelijak číslované, patrně pokusné
laboratoře či co, roztroušené mezi pískovými násypy a borovými lesíky. Jeho
cesta se stočila k docela osamělému domku V, 7, i zamířil k němu. Na dveřích
byla mosazná tabulka „Ing. Prokop“. Prokop odemkl klíčem, který mu dal Carson,
a vešel dovnitř.
Byla tam vzorně zařízená laboratoř pro chemii třaskavin – tak moderní a úplná,
že se v Prokopovi zatajil dech radostí odborníka. Na hřebíku visela jeho stará
halena, v rohu vojenský kavalec tak jako v Praze, a v přihrádkách velkolepě
vybaveného psacího stolu ležely pečlivě spořádány a zkatalogizovány veškeré
jeho tištěné články a rukopisné poznámky.


XXV.

Půl roku neměl Prokop v rukou milované nádobíčko chemika.
Prohlížel nástroj po nástroji; bylo tu vše, o čem kdy mohl snít, lesklé,
zbrusu nové a zrovna výstavní ve své pedantické uspořádanosti. Byla tu
příruční a odborná knihovna, ohromný regál s chemikáliemi, skříně s citlivými
nástroji, tlumicí kabina na pokusné exploze, komora s transformátory, zkušební
aparáty, které ani neznal; prohlédl sotva polovinu těch divů divoucích, když
poslušen okamžitého nápadu se hnal k regálu pro nějakou sůl barya, kyselinu
dusičnou a ještě cosi a zahájil pokus, při němž se mu povedlo ožehnout si
prst, přivést zkumavku k prasknutí a propálit si díru do kabátu; tu uspokojen
usedl k psacímu stolu a načmáral dvě tři poznámky.
Potom jal se znovu okukovat laboratoř. Trochu mu to připomínalo nově zařízenou
parfumerii; bylo to příliš uspořádané, ale stačilo sáhnout na to a ono, aby to
rozházel po své chuti; tak, teď to tu vypadá intimněji. Uprostřed
nejhorlivější práce se zarazil: Aha, řekl si, tímhle tedy mne chtějí dostat na
lep! Za chvíli přijde Carson a začne hučet: budete big man a kdesi cosi.
Sedl si mračně na kavalec a čekal. Když nikdo nepřicházel, šel jako zloděj k
pultu a hrál si znovu s barnatou solí. Beztoho jsem tu naposledy, chlácholil
sebe sama. Pokus se podařil dokonale: prsklo to s dlouhým plamenem a skleněný
zvon na citlivých váhách praskl. „Teď dostanu,“ hrklo v něm provinile, když
uviděl dosah škody, a vytratil se z laboratoře jako školák, který rozbil okno.
Venku byl už soumrak a drobně pršelo. Deset kroků před barákem stála vojenská
hlídka.
Prokop zamířil pomalu k zámku cestou, po které byl přišel. V parku nebylo živé
duše; jemný déšť šuměl v korunách stromů, v zámku svítili a klavír bouřil do
šera vítěznou písní. Prokop se pustil do oné pusté části parku mezi hlavním
východem a terasou. Zarůstalo to tu bezcestně, i zaryl se do vlhkého křoví
jako kanec, naslouchaje chvílemi a opět si raze cestu praskajícím houštím.
Tady je konečně kraj džungle, kde křoví se překlání přes starou hradbu ne
vyšší v těchto místech než tři metry. Prokop se chytil převislých větví, aby
se po nich spustil dolů; ale pod jeho solidní tíhou větve se zlomily s ostrým
třeskem, jako když z pistole střelí, a Prokop dopadl s těžkým žuchnutím na
jakési smetiště. Zůstal sedět s tlukoucím srdcem: teď někdo na mne přijde.
Nebylo slyšet nic než šustění deště. I sebral se a hledal zeď se zelenými
vrátky, jak ji byl viděl ve snu.
Bylo to tak, až na jednu okolnost: že vrátka byla pootevřena. Znepokojil se
velmi: buď jimi někdo právě vyšel, nebo se tudy vrátí; v obojím případě je
někde nablízku. Co tedy činit? Rychle rozhodnut kopl Prokop do vrátek a vyšel
rázně na silnici; a opravdu, potloukal se tam nevelký člověk v gumáku a
pokuřoval lulku. Tak stáli oba proti sobě v jistém zmatku, kdo začne a s čím.
Začal ovšem agilnější Prokop. Zvoliv bleskově mezi několika možnostmi cestu
násilí, vrhl se na muže s lulkou a beraním nárazem své hrubé síly jej okamžitě
položil do bláta. Nyní ho tiskne hrudí a lokty k zemi trochu udiven a nevěda,
co s ním teď; neboť nemůže ho přece zaškrtit jako slepici. Člověk pod ním ani
nepouští faječku z úst a patrně vyčkává. „Vzdej se,“ supí Prokop, ale v tom
okamžiku dostal ránu kolenem do břicha a pěstí pod čelist, a skutálel se do
příkopu.
Když se počal sbírat, čekal novou ránu; ale muž s faječkou stál klidně na
silnici a pozoroval ho. „Ještě?“ vycedil. Prokop zavrtěl hlavou. Tu jal se mu
ten chlapík ohromně špinavým kapesníkem čistit šaty. „Bláto,“ poznamenal a
třel co nejradikálněji.
„Zpátky?“ řekl konečně a ukazoval na zelená vrátka. Prokop chabě souhlasil.
Člověk s lulkou ho tedy vedl zpět až k staré hradbě a sklonil se, opřen rukama
o kolena. „Lezte,“ kázal suše. Prokop se mu postavil na ramena, člověk se
vztyčil a děl: „Hop!“ Prokop se zachytil převislého křoví a vydrápal se na
hradbu. Bylo mu skoro do pláče hanbou.
A ještě to, ještě to ke všemu: Když poškrabán a zpuchlý, uválen v blátě,
strašný a ponížený se kradl po zámeckých schodech do svého „kavalírského“
pokoje, potkala ho princezna Wille. Prokop se chtěl tvářit, jako by to nebyl
on nebo jako by jí neznal či co, zkrátka nepozdravil a hnal se nahoru jako
monument z bláta; a jak uháněl podle ní, zachytil její udivený, povýšený,
vskutku velmi urážlivý pohled. Prokop stanul jako udeřen. „Počkat,“ křikl a
seběhl k ní, a čelo mu zrovna praskalo náběhem vzteku. „Jděte,“ křičel, „a
řekněte jim, řekněte jim, že… že na ně kašlu a… že se nedám zavřít, rozumíte?
Nedám,“ zařval a uhodil pěstí do zábradlí, až zadrnčelo; načež se vyřítil zase
do parku nechávaje za sebou princeznu bledou a přímo ztuhlou.
Několik okamžiků nato vpadl kdosi k nepoznání zablácený do domku vrátného,
převrhl dubový stůl na večeřícího stařečka, popadl Boba za krk a škrtl mu
hlavou o zeď tak, že ho napolo skalpoval a nadobro omráčil; načež se zmocnil
klíče, odemkl vrata a běžel ven. Tam narazil na hlídkujícího vojáčka, jenž
ihned vykřikl výstrahu a strhl pušku; ale než mohl střelit, začal se s ním ten
kdosi cloumat, vyrval mu pušku z ruky a zlomil mu pažbou klíční kost. Tu však
přibíhaly dvě nejbližší hlídky; temná postava hodila po nich puškou a vrhla se
zpět do parku.
Skoro v témž okamžiku byl přepaden noční hlídač u východu C: kdosi černý a
veliký ho začal zčistajasna častovat strašnými ranami do spodní čelisti.
Hlídač, plavý obr, nadmíru překvapen chvíli držel, než ho napadlo zahvízdat;
tu ho ten někdo hrozně klna pustil a běžel zpět do černého parku. Pak byly
zalarmovány posily a četné patroly procházely parkem.
Asi o půlnoci demoloval kdosi balustrádu na parkové terase a vrhal
desetikilové kameny po stráži, jež přecházela dole v hloubi deseti metrů.
Voják vystřelil, načež shora se vychrlila spousta politických urážek, a bylo
ticho. V tu chvíli přijížděli z Dikkeln přivolaní kavaleristé, zatímco veškerá
balttinská posádka šťouchala bajonety do křovin. V zámku už dávno nikdo
nespal. V jednu hodinu našli u tenisového hříště omráčeného vojáka bez pušky.
Brzo nato se v březovém lesíku strhla krátká, ale vydatná přestřelka; raněn
nebyl bohudík nikdo. Pan Carson s tváří ustaranou důtklivě posílal domů
princeznu Wille, jež chvějíc se nejspíš nočním chladem bůhvíproč se odvažovala
na bojiště; ale princezna s očima podivně velikýma řekla, aby ji laskavě
opustil. Pan Carson pokrčil rameny a nechal ji bláznit.
Ač kolem zámku bylo lidí jako much, jal se někdo z houští metodicky vytloukat
zámecká okna. Nastal zmatek, neboť současně padly dvě tři rány z pušky až na
silnici. Pan Carson vypadal náramně znepokojeně.
Zatím princezna ani nedutajíc putovala cestičkou červených buků. Najednou se
proti ní řítila ohromná černá postava, stanula před ní, zahrozila pěstí a
drtila cosi, že je to hanba a skandál; pak se ponořila do houští, jež praskalo
a střásalo těžkou vláhu deště. Princezna se vrátila a zadržela patrolu: že prý
tam nikdo není. Její oči byly rozšířené a lesklé, jako by měla horečku. Po
chvíli se rozlehla střelba z křovin za rybníkem; podle zvuku to byly
brokovnice. Pan Carson huboval, aby se ti pacholci ze dvora do toho nepletli,
nebo že je vytahá za uši. V té chvíli ještě nevěděl, že tam kdosi utloukl
kamenem skvostnou dánskou dogu.
Za úsvitu našli Prokopa tvrdě spícího na lehátku v japonském altánu. Byl
úžasně rozdrásán a zablácen a šaty na něm visely v cárech; na čele měl bouli
jako pěst a vlasy spečené krví. Pan Carson potřásl hlavou nad spícím hrdinou
noci. Potom se přišoural pan Paul a pečlivě přikryl chrupajícího spáče teplou
houní; pak přinesl i umyvadlo s vodou a ručník, čisté prádlo a zbrusu nové
sportovní šaty od pana Drehbeina, a po špičkách odešel.
Jen dva nenápadní muži v civilu, s revolvery v zadní kapse, se procházeli až
do rána v blízkosti japonského altánu s nenucenou tváří lidí, kteří dohlížejí
na východ slunce.


XXVI.

Prokop čekal, kdoví co že bude následovat po oné noci; nenásledovalo nic, či
spíše následoval ho onen člověk s lulkou – jediný, kterého se Prokop jaksi
bál. Ten člověk se jmenoval Holz, – jméno, jež povídalo velmi málo o jeho
tiché a bdělé podstatě. Kamkoliv se Prokop hnul, pohyboval se svých pět kroků
za nim; Prokopa to divoce dráždilo a týral ho po celý den způsobem
nejrafinovanějším: například pobíhal sem tam, sem tam po krátké cestičce
padesátkrát a stokrát, čekaje, že pana Holze omrzí udělat vždycky po dvaceti
krocích čelem vzad; pana Holze to však neomrzelo. I jal se Prokop utíkat a
běžel třikrát round celým parkem; pan Holz mlčky uháněl za ním a ani nepřestal
pouštět obláčky kouře, zatímco Prokop se udýchal, až to v něm hvízdalo.
Pan Carson se toho dne ani neukázal; patrně se hněval. Kvečeru se Prokop
sebral a putoval k své laboratoři, provázen ovšem svým mlčelivým stínem. V
laboratorním baráku chtěl za sebou zamknout; ale pan Holz vstrčil nohu mezi
dveře a vešel za ním. A protože byla v předsíni přichystána lenoška, bylo
zřejmo, že se pan Holz odtud nehne. Nu, taky dobře. Prokop kutil v laboratoři
něco tajemného, zatímco pan Holz v předsíni suše a krátce chrápal. Ke druhé
hodině zrána napouštěl Prokop jakýsi motouz petrolejem, zapálil jej a uháněl
ven, jak rychle jen dovedl. Pan Holz vyletěl okamžitě z lenošky a pustil se za
ním. Po stu krocích vrhl se Prokop do příkopu tváří k zemi; pan Holz zůstal
nad ním stát a rozžíhal si lulku. Prokop zvedl hlavu a chtěl mu něco říci, ale
spolkl to, neboť si vzpomněl, že s Holzem zásadně nemluví; zato vztáhl ruku a
podrazil mu nohy. „Pozor,“ zahučel, a v tom okamžiku zarachotil v baráku
důkladný výbuch a tříšť kamení i skla jim letěla nad hlavou, jen to svištělo.
Prokop vstal, tak tak se očistil a rychle běžel odtud, sledován panem Holzem.
V tu chvíli již se sbíhaly stráže a najíždělo auto s hasiči.
To byla první výstraha adresovaná panu Carsonovi. Nepřijde-li teď vyjednávat,
nastanou věci horší.
Pan Carson nepřišel; místo návštěvy došla nová legitimace patrně pro jiný
pokusný barák. Prokop se rozlítil. Dobrá, řekl si, tentokrát jim ukážu, co
dovedu. Poklusem běžel do své nové laboratoře, vybíraje v mysli pádnější
projev svého protestu; rozhodl se pro třaskavé draslo, jež se zanítí vodou.
Avšak u nového baráku mu klesly bezmocně ruce: Zatraceně, je ten Carson ďábel!
Hned s laboratoří totiž sousedily domky patrně pro závodní hlídače; na
zahrádce se popelil dobrý tucet dětí a mladá maminka tam konejšila řvoucího
červeného živočicha. Když viděla zuřivý pohled Prokopův, zarazila se a
přestala zpívat. „Dobrý večer,“ zabručel Prokop a loudal se zpátky s pěstmi
zaťatými. Pan Holz pět kroků za ním.
Cestou do zámku potkal princeznu na koni s celou kavalkádou důstojníků. Uhnul
na postranní cestu, ale princezna v trysku stočila koně za ním. „Chcete-li si
vyjet,“ řekla rychle, a její tmavou lící prokmitla vlna krve, „je vám k
dispozici Premier.“
Prokop couval před tančícím Whirlwindem. Jakživ neseděl na koni, ale nepřiznal
by se k tomu za nic na světě. „Děkuji,“ řekl, „není třeba… oslazovat… mé
vězení.“
Princezna se zakabonila; bylo ovšem nemístné mluvit právě s ní o této stránce
věci; avšak přemohla se a děla, hladce shrnujíc výtku i pozvání:
„Nezapomínejte, že na zámku jste hostem u mne.“
„Myslím, že o to nestojím,“ mručel Prokop tvrdohlavě, dávaje pozor na každý
pohyb nervózního koně.
Princezna podrážděně trhla nohou; Whirlwind zafrkal a počal se vzpínat.
„Nebojte se ho,“ hodila Wille s úsměškem.
Prokop se zamračil a uhodil koně po hubě; princezna vzala bičík, jako by ho
chtěla švihnout přes ruku. Všechna krev valila se Prokopovi do hlavy. „Pozor,“
zaskřípěl a zahryzl se rudýma očima do princezniných jiskřících. Ale tu již
zpozorovali důstojníci trapný případ a cválali k princezně. „Halloh, co se
děje?“ volal ten, který jel v čele na černé klisně a hnal svého koně rovnou na
Prokopa. Prokop viděl nad sebou koňskou hlavu, i popadl ji za udidlo a vší
silou ji strhl stranou. Kůň zařičel bolestí a vzepjal se tanče na zadních
nohou, zatímco důstojník letěl do náručí klidného pana Holze. Dvě šavle
zaplály ve slunci; ale vtom tu byla princezna na třesoucím se Whirlwindu a
tlačila jeho bokem důstojníky nazpátek.
„Nechat,“ kázala, „je to můj host!“ Přitom šlehla po Prokopovi temným pohledem
a dodala: „Ostatně se bojí koní. Pánové se spolu seznámí. Poručík Rohlauf.
Inženýr Prokop. Kníže Suwalski. Von Graun. Případ je vyřízen, že ano? Rohlauf
na koně a jedeme. Premier je vám k dispozici, pane. Tedy pamatujte, že tady
jste jen hostem. Na shledanou!“ Bičík mnohoslibně zasvištěl vzduchem,
Whirlwind se zatočil, až písek tryskal, a kavalkáda zmizela v ohybu cesty; jen
pan Rohlauf obtancoval na koni Prokopa, spaloval ho zuřivýma očima a vypravil
ze sebe hlasem vztekle zajiklým: „Bude mne těšit, pane!“
Prokop se otočil na patě, šel do svého pokoje a zamkl se; po dvou hodinách
putoval po stařičkých nohou Paulových jakýsi dlouhý dopis z „kavalírského
pokoje“ na ředitelství. Vzápětí běžel pan Carson s čelem přísně svraštělým k
Prokopovi; velitelským posuňkem vyhnal pana Holze, který pokojně dřímal na
židli před pokojem, a vnikl dovnitř.
Pan Holz si tedy sedl před zámkem a zapálil si lulku. Uvnitř se strhl strašný
řev, ale to se Holze pranic netýkalo; protože mu faječka netáhla, rozšrouboval
ji a znalecky ji protahoval stéblem. Z „kavalírského pokoje“ se ozývalo
chroptění dvou tygrů do sebe zakousnutých; jeden řval a druhý soptil, bouchl
nějaký nábytek, byl okamžik ticha a opět zatřeskl strašlivý křik Prokopův.
Sbíhali se zahradníci, ale pan Holz je zahnal pokynem ruky a jal se
profoukávat troubel. Burácení nahoře rostlo, oba tygři ryčeli a doráželi na
sebe chraptíce zběsilostí. Pan Paul vyběhl ze zámku bled jako stěna a zvedal
uděšené oči k nebi. V tu chvíli klusala tudy princezna se svým průvodem; když
uslyšela boží dopuštění v hostinském křídle zámku, zasmála se nervózně a
docela zbytečně švihla Whirlwinda bičem. Pak se křik poměrně utišil; bylo
slyšeti hromování Prokopovo, jenž něčím vyhrožuje a bouchá pěstí do stolu. Do
toho mu vpadá ostrý hlas, jenž hrozí a poroučí; Prokop řve horečné protesty,
ale břitký hlas odpovídá tiše a rozhodně.
„Jakým právem?“ křičí hlas Prokopův. Velitelský hlas cosi vysvětluje s
příšernou a tichou důtklivostí. „Ale pak, rozumíte, pak vyletíte všichni do
povětří,“ burácí Prokop, a vřava se rozpoutává nanovo tak hrozně, že pan Holz
rázem stopil lulku do kapsy a rozběhl se k zámku. Ale opět to utichlo, jen
ostrý hlas kázal a odsekával věty, doprovázen temným a hrozivým mručením; bylo
to, jako když se diktují podmínky příměří. Ještě dvakrát se rozkatil divý řev
Prokopův, ale ostrý hlas už se nerozčiloval; zdálo se, že si je jist svou
věcí.
Po půldruhé hodině vyrazil pan Carson z pokoje Prokopova, fialový a lesklý
potem, funící a nachmuřený, a běžel poklusem k pokojům princezniným. Deset
minut nato pan Paul, třesa se úctou, hlásil Prokopovi, jenž hryzl si rty a
prsty ve svém pokoji: „Její Jasnost.“
Vešla princezna ve večerních šatech, popelavě bledá a s obočím palčivě
staženým. Prokop jí pokročil vstříc a chtěl, jak se zdálo, něco říci; ale
princezna jej zadržela pohybem ruky, pohybem, jenž byl pln výsosti a odporu, a
řekla zadrhlým hlasem: „Jdu se vám… pane… omluvit za onen výstup. Nemínila
jsem vás šlehnout. Lituji toho nesmírně.“
Prokop zrudl a chtěl opět něco říci; ale princezna pokračovala: „Poručík
Rohlauf dnes odjede. Kníže prosí, abyste někdy přišel k našemu stolu.
Zapomeňte na tu příhodu. Na shledanou.“ Rychle mu podala ruku; Prokop se sotva
dotkl jejích prstů. Byly velmi chladné a jako mrtvé.


XXVII.

Nuže, po bouřce s Carsonem jako by se vyčistil vzduch. Prokop sice prohlásil,
že při nejbližší příležitosti uteče, ale zavázal se čestným slovem, že až do
té doby se zdrží všech násilností a výstrah; za to byl pan Holz odsunut do
vzdálenosti patnácti kroků a Prokopovi dovoleno v jeho průvodu se volně
pohybovat v okruhu čtyř kilometrů od sedmi ráno do sedmi večer, spát v
laboratoři a stravovat se, kde mu libo. Naproti tomu však nasadil mu Carson
přímo do laboratoře ženu s dvěma dětmi, náhodou zrovna vdovu po dělníkovi
zabitém při výbuchu Krakatitu, jako jisté morální rukojemství proti jakékoliv
(řekněme) neopatrnosti. Krom toho vysazen Prokopovi znamenitý plat ve zlatě a
necháno mu na vůli, aby se prozatím bavil nebo zaměstnával, jak mu libo.
První dny po této dohodě strávil Prokop tím, že všemožně prostudoval terén v
okruhu čtyř kilometrů co do možnosti útěku. Byla prašpatná vzhledem k hlídkové
zóně, jež fungovala přímo výtečně. Prokop vymyslel několik způsobů, jak zabíti
Holze; naneštěstí shledal, že tento suchý a houževnatý patron živí pět dětí a
krom toho matku a chromou sestru, a že má ještě ke všemu za sebou tři léta
káznice pro zabití člověka. Tyto okolnosti nebyly příliš povzbuzující.
Jistou útěchou Prokopovi bylo, že se do něho oddaně, přímo náruživě zamiloval
pan Paul, klíčník na penzi, dokonale šťastný, že má komu sloužit; neboť
jemného stařečka tuze trápilo, že byl shledán příliš pomalým, aby posluhoval
při knížecí tabuli. Prokop si někdy až zoufal pro jeho obtížné a uctivé
pozornosti. Mimoto náramně přilnul k Prokopovi doktor Krafft, Egonův
vychovatel, člověk zrzavý jako liška a hrozně nešťastný v životě; byl přímo
neobyčejně vzdělaný, trochu teozof a k tomu nejpošetilejší idealista, jakého
si lze představit. K Prokopovi se blížil pln ostychu a obdivoval se mu
bezuzdně, neboť jej považoval přinejmenším za génia. Skutečně znal už dávno
Prokopovy odborné články, a dokonce na nich budoval teozofický výklad
nejnižšího okruhu, čili abych tak po sprostu řekl, hmoty. Nadto byl pacifista
a otrava jako všichni lidé příliš ušlechtilých názorů.
Prokopa konečně omrzelo bezcílné potloukání podle hlídkové zóny, a vracel se
stále častěji do laboratoře, aby pracoval na svých věcech. Studoval své staré
poznámky a doplňoval mnohé mezery; sestrojil a opět zničil dlouhou řadu
třaskavin, jež potvrzovaly jeho nejodvážnější hypotézy. Byl téměř šťasten v
těchto dnech; avšak večer, večer se vyhýbal lidem a tesknil pod klidným
dohledem pana Holze, dívaje se na oblaka, na hvězdy a na volný obzor.
Ještě jedna věc ho kupodivu zaměstnávala: jakmile zaslechl dupot koňských
kopyt, přistoupil k oknu a pozoroval jezdce, ať to byl štolba nebo některý
důstojník nebo princezna (s níž nemluvil od onoho dne), a s očima nachmuřenýma
samou pozorností zkoumal, jak se to dělá. Shledával, že jezdec vlastně nesedí
jen tak v sedle, nýbrž do jisté míry stojí ve třmenech; že nepracuje zadkem,
nýbrž koleny; že není trpně jako pytel brambor natřásán koňským cvalem, nýbrž
aktivně vystihuje jeho periodicitu. To vše je prakticky snad velmi jednoduché,
ale pro inženýrského pozorovatele je to mechanismus náramně spletitý,
jmenovitě jakmile kůň začne vzpínat se nebo vyhazovat nebo tančit třesa se
ušlechtilou a nedůtklivou plachostí. To vše studoval Prokop dlouhé hodiny
skryt za okenní záclonou; a jednoho pěkného rána nařídil Paulovi, aby mu dal
osedlat Premiera.
Pan Paul byl velmi zaražen; vysvětloval, že Premier je ohnivý a málo oježděný
rap hrozně nesnášenlivý, avšak Prokop krátce opakoval rozkaz. Jízdecké šaty
měl připraveny ve skříni; oblékl je se slabým pocitem ješitnosti a hnal se na
dvůr. Tam už tancoval Premier tahaje za sebou štolbu, jenž ho držel u huby.
Jako to viděl u jiných, chlácholil Prokop koně hladě mu nozdry a lysinu.
Valach se trochu utišil, jen nohy mu hrály v plavém písku. Prokop se k němu
obmyslně blížil z boku; užuž zvedal nohu ke třmenu, když Premier bleskově po
něm sekl zadní nohou a uhnul zadkem, že Prokop stěží měl čas uskočit. Štolba
vyprskl v krátký smích; to stačilo; Prokop se útokem vrhl na koňův bok,
neznámo jak dostal špičku nohy do třmene a vymrštil se. V nejbližších
okamžicích nevěděl, co se děje; všecko se zatočilo, někdo vykřikl, Prokop měl
jednu nohu ve vzduchu, zatímco druhá nemožně uvázla ve třmenu; nyní Prokop
těžce dopadl do sedla a sevřel kolena vší silou. To mu vrátilo vědomí právě ve
chvíli, kdy Premier vyhodil zadkem jako střelen; Prokop se honem položil
nazad, znovu dopadl a křečovitě přitáhl uzdu. Následkem toho se bestie
postavila na zadní nohy jako svíce; Prokop svíral kolena jako kleště a položil
se tváří až mezi rapovy uši, úzkostlivě dbaje, aby ho neobjal kolem krku,
neboť se bál, že by to vypadalo směšně. Visel vlastně jen na kolenou. Premier
se postavil zase na všechny čtyři a počal se točit jako vlček; toho použil
Prokop k tomu, aby dostal špičku druhé nohy do třmene. „Netiskněte ho tak,“
volal štolba, ale Prokop byl rád, že má koně mezi koleny. Valach se spíš
zoufale než zlomyslně snažil shodit svého divného jezdce; točil se a
vyhazoval, až písek tryskal, a celý kuchyňský personál vyběhl na dvůr podívat
se na tento divoký cirkus. Prokop zahlédl pana Paula, jenž úzkostí tiskl
ubrousek k ústům, a dr. Krafft se vyřítil, svítě na slunci svou zrzavou
hlavou, a s nasazením vlastního života chtěl zadržet Premiera za udidla.
„Nechte ho,“ křikl Prokop v bezuzdné pýše, a bodl valacha do slabin. Pane na
nebi! Premier, kterému se tohle ještě nestalo, vyrazil jako šíp a letěl ze
dvora do parku; Prokop stáhl hlavu mezi ramena, počítaje s tím, aby spadl
okrouhleji, až poletí; jinak stál ve třmenech nakloněn kupředu, mimovolně
napodobuje závodní žokeje. Když se takto řítil podle tenisového hříště,
zahlédl tam několik bílých figurek; tu ho popadlo furiantství a začal
traktovat bičem Premierovu kýtu. Nyní zdivočelý rap ztratil hlavu nadobro; po
několika nepříjemných skocích na bok sedl na zadek a zdálo se, že se překotí;
ale místo toho vyrazil přes záhony jako ztřeštěný. Prokop chápal, že nyní
záleží vše na tom, udržet mu hlavu nahoře, nemají-li oba udělat kotrmelec na
terénu tak nespolehlivém, i visel na uzdě a táhl. Premier se vzepjal, naráz
pokryt potem, a zničehonic začal rozumně cválat. Bylo to vítězství.
Prokopovi se nesmírně ulevilo; teprve nyní mohl vyzkoušet, co studoval tak
důkladně, totiž akademickou školu jezdce v sedle. Třesoucí se kůň poslouchal
uzdy jedna radost, a Prokop, pyšný jako bůh, točil jej po vinutých cestách
parku míře zpátky k tenisovému hříšti. Už viděl za křovím princeznu s raketou
v ruce, i pobodl Premiera do galopu. Vtom princezna mlaskla jazykem, Premier
se vznesl do vzduchu a letěl k ní přes křoví jako šíp; a Prokop naprosto
nepřipraven na tuhle vysokou školu vyletěl ze třmenů a poroučel se přes koňovu
hlavu do trávy. V tu chvíli cítil, že něco zapraskalo, a na vteřinu se mu
bolestí obestřely smysly.
Když procitl, viděl princeznu a tři pány v onom zaraženém postoji lidí, kteří
nevědí, mají-li se smát povedené švandě, nebo přiběhnout na pomoc. Prokop se
opřel o lokty a pokusil se pohnout levou nohou, jež ležela pod ním divně
stočena. Princezna pokročila s tázavým a trochu již ulekaným pohledem.
„Tak,“ řekl Prokop tvrdě, „teď jste mi zlomila nohu.“ Trpěl hrozně a vědomí se
mu mátlo otřesem; přesto se pokoušel vstát. Když zase přišel k sobě, ležel v
princeznině klíně a Wille mu utírala zpocené čelo pronikavě vonícím
kapesníkem. Přes strašlivou bolest v noze byl napolo jako v snách. „Kde je…
kůň,“ blábolil a počal sténati, když jej dva zahradníci kladli na přinesenou
lavici a nesli do zámku. Pan Paul se změnil ve všecko na světě: v anděla,
milosrdnou sestru a rodnou matku, pobíhal, rovnal Prokopovi pod hlavou podušky
a kapal mu na rty koňak; pak si musel sednout vedle postele, a Prokop mu
mačkal ruku v poryvech bolesti, posilován dotykem té měkké a stařecky lehýnké
ruky. Dr. Krafft stál u nohou s očima plnýma slz, a i pan Holz zřejmě dojat
rozstřihoval Prokopovi jezdecké nohavice a máčel mu stehno studenými obklady.
Prokop tiše sténal a chvílemi se modrými rty usmíval na Kraffta nebo na pana
Paula. A tu již se přivalil plukovní lékař, takový lepší řezník, provázen
asistentem, a bez dlouhých okolků se pustil do Prokopovy nohy. „Hmjo,“ řekl,
„komplikovaná fraktura femoris a tak dále; nejmíň šest neděl postele,
člověče.“ Vybral dvě dyhy, a nyní se počala trapná věc. „Natahujte mu nohu,“
kázal řezník asistentovi; ale pan Holz uctivě odstrčil rozčileného nováčka a
chopil se sám zlomeného údu celou svou tvrdou, šlachovitou silou. Prokop se
zahryzl do podušky, aby neřval bolestí jako zvíře, a vyhledal očima utrápenou
tvář pana Paula, na níž se zrcadlila všechna jeho vlastní muka. „Ještě
kousek,“ basoval doktor ohmatávaje frakturu; Holz mlčky a pevně táhl. Krafft
prchl koktaje cosi v úplném zoufalství. Nyní řezník rychle a obratně utahoval
dyhy; přitom bručel, že zítra zaleje tu sakramentskou nohu do sádry. Konečně
je po všem; bolí to sice příšerně a natažená noha leží jako mrtvá, ale aspoň
ten řezník je pryč; jenom pan Paul přechází po špičkách a žbrblaje měkkými rty
se stará, jak by trpiteli ulevil.
Tu se přižene pan Carson autem a bera najednou čtyři schody letí k Prokopovi.
Pokoj se naplní jeho třesknou účastí, hned je tu veseleji a jaksi chlapácky;
pan Carson žvaní pro útěchu páté přes deváté, a najednou pohladí Prokopa
nesměle a přátelsky po zježené hlavě; v tu chvíli odpouští Prokop svému
zavilému nepříteli a tyranu devět desetin jeho špatností. Pan Carson se
přehnal jako vítr; a nyní se posunuje po chodbě cosi těžkého, dveře se rozletí
a dva lokajové s bílými prackami vedou dovnitř ochromeného knížete. Kníže už
ode dveří kývá úžasně vyschlou a dlouhou ručkou, aby snad Prokop ze samé úcty
zázračně nevstal a nevykročil vstříc Jeho Jasnosti; pak se nechá posadit a
vypraví ze sebe několik vět nejblahovolnější účasti.
Sotva zmizelo toto zjevení, ťuká někdo na dveře a pan Paul šeptá s nějakou
komornou. Hned nato vchází princezna, má ještě bílé tenisové šaty a v hnědé
tváři vzdor a kajícnost; neboť se přichází dobrovolně omluvit ze svého
hrozného uličnictví. Ale než může promluvit, rozzáří se drsný, hrubě omítnutý
Prokopův obličej dětským úsměvem. „Tak co,“ praví pyšně pacient, „bojím se
koně nebo ne?“
Princezna se zarděla tak, že by to nikdo do ní neřekl; až ji to samu zamrzelo
a uvedlo do rozpaků. Nicméně se přemohla, a rázem je z ní vznešená hostitelka;
hlásí, že přijede chirurg profesor, a ptá se, co si Prokop žádá k jídlu, ke
čtení a podobně; ještě nařídí Paulovi, aby dvakrát denně jí podával zdravotní
zprávu, jaksi z dálky urovná cosi na polštáři a s malým kývnutím hlavy odejde.
Když zanedlouho přijel slavný chirurg autem, bylo mu několik hodin čekati, byť
nad tím sebevíc kroutil hlavou. Pan inženýr Prokop totiž ráčil hluboce usnout.


XXVIII.

To se rozumí, slavný chirurg neuznal práci vojenského řezníka, roztahoval
znovu Prokopovy zlomeniny a nakonec to vše zalil do sádry a řekl, že levá
extremita zůstane podle všeho chromá.
Prokopovi nastaly dny slavné a ležácké. Krafft mu předčítal Swedenborga a pan
Paul rodinné kalendáře, zatímco princezna dala obklopit lůžko trpitele všemi
nádhernými vazbami světové literatury. Nakonec Prokopa omrzely i kalendáře a
jal se Krafftovi diktovat soustavné dílo o destruktivní chemii. Nejvíc si –
kupodivu – oblíbil Carsona, jehož drzost a bezohlednost mu imponovala; neboť
našel pod ní veliké plány a potrhlou fanatičnost zásadního, mezinárodního
militaristy. Pan Paul byl na vrcholu blaženství; nyní byl nepostrádatelný od
noci do noci a mohl sloužiti každým dechem a každým krůčkem svých šouravých
nohou.
Ležíš sevřen hmotou, podoben poraženému pni; ale což necítíš jiskření
strašlivých a neznámých sil v té nehybné hmotě, jež tě poutá? Hovíš si na
prachových poduškách nabitých větší silou než sud dynamitu; tvé tělo je spící
třaskavina, a i třesoucí se, zvadlá ručička Paulova skrývá v sobě větší možnou
brizanci než melinitová kapsle. Spočíváš nehnutě v oceánu nezměřitelných,
nerozložených, nevypáčených sil; co je kolem tebe, nejsou pokojné zdi, tiší
lidé a hučící koruny stromů, nýbrž muniční sklad, kosmická prachárna
připravená k strašlivému výkonu; ťukáš kloubem na hmoty, jako bys přehlížel
sudy ekrazitu, zkoumaje, jak jsou plné.
Prokopovy ruce zprůhledněly nehybností, ale nabyly zato podivného hmatu:
cítily zrovna a odhadovaly detonační potenciál všeho, čehokoliv se dotkly.
Mladé tělo má ohromné brizantní napětí; ale opět dr. Krafft, nadšenec a
idealista, obsahuje poměrně slabou výbušnou kvalitu, kdežto Carsonovo
detonační číslo se blíží tetranitranilinu; a Prokop se zachvěním vzpomínal na
chladný dotek princezniny ruky, jenž mu vyzradil příšernou brizanci toho
zpupného amazonského tvora. Prokop si lámal hlavu, závisí-li potenciální
třaskavá energie organismu na přítomnosti nějakých enzymových či jakých látek,
nebo na chemické stavbě samotných buněčných jader, jež jsou náboji par
excellence. Ať je tomu jakkoliv: rád bych viděl, jak by ta černá, nadutá holka
explodovala.
Teď už pan Paul vozí Prokopa v lenošce po parku; pan Holz je nyní zbytečný,
ale činí se, neboť byl v něm objeven veliký talent masérský, a Prokop cítí z
jeho tuhých prstů zrovna prýštit blahodárnou explozívní sílu. Potká-li někdy
princezna pacienta v parku, promluví něco s dokonalou a přesně odměřenou
zdvořilostí, a Prokop k svému vzteku nikdy nepochopí, jak se to dělá; neboť
sám je buď příliš hrubý, nebo příliš sdílný. Ostatní společnost vidí v
Prokopovi podivína; to jim dává právo nebrat ho vážně a jemu volnost býti k
nim nezdvořilý jako drvoštěp. Jednou se princezna ráčila u něho zastavit s
celým průvodem; nechala pány stát, usedla vedle Prokopa a ptala se ho po jeho
práci. Prokop, chtěje jí co nejvíce vyhovět, upadl do tak odborného výkladu,
jako by měl přednášku na mezinárodním kongresu chemiků; kníže Suwalski a
jakýsi cousin se začali šťouchat a smát, a tu se Prokop rozvzteklil a utrhl se
na ně, jim že to nepovídá. Všechny oči se obrátily na Její Jasnost, neboť na
ní bylo usadit nesrstného plebejce; ale princezna se trpělivě usmála a poslala
pány hrát tenis. Zatímco se za nimi dívala očima jako kmín přimhouřenýma,
zpytoval ji Prokop úkosem; vlastně poprvé si jí pořádně všiml. Byla tuhá,
tenká, s nadbytkem pigmentu v pleti, vlastně ne zrovna hezká; maličká ňadra,
nohy přehozeny, skvostné rasové ruce; na pyšném čele jizva, oči skryté a
prudké, pod ostrým nosem temné chmýří, zpupné a tvrdé rty; nu ano, vlastně
téměř hezká. Jaké má opravdu oči?
Tu je k němu plně obrátila, a Prokop se zmátl. „Prý umíte hmatem poznat
povahu,“ řekla honem. „Vypravoval o tom Krafft.“ Prokop se zasmál tomuto
ženskému výkladu své zvláštní chemotaxe. „Nu ano,“ povídal, „člověk cítí,
kolik má která věc síly; to nic není.“ Princezna pohlédla rychle na jeho ruku
a potom kolem dokola; nebyl tam nikdo.
„Ukažte,“ zabručel Prokop a nastavil rozjizvenou dlaň. Položila na ni hladké
konečky prstů; nějaký blesk proběhl Prokopem, srdce mu zabouchalo a hlavou mu
nesmyslně kmitlo: „Což kdybych sevřel!“ A již hnětl a drtil v hrubé tlapě
tuhé, palčivé maso její ruky. Opilá závrať mu zaplavila hlavu; viděl ještě, že
princezna zavírá oči a syká rozchlípenými rty, sám pak semkl oči a zatínaje
zuby propadal se do kroužící tmy; jeho ruka se horce a divoce rvala s tenkými,
přísavnými prsty, které se mu chtěly vyrvat, které se hadovitě svíjely, které
se zarývaly nehty do jeho kůže a opět naze, křečovitě přilnuly k jeho masu.
Prokop jektal zuby rozkoší; chvějivé prsty pekelně dráždily jeho zápěstí,
začal vidět rudá kola, náhle prudký a žhavý stisk, a úzká ruka se mu vydrala z
dlaně. Omámen zvedl Prokop opilá víčka; v hlavě mu hučelo těžkými tepy; s
úžasem viděl opět zelenou a zlatou zahradu a musel přivřít oči oslněn denním
světlem. Princezna zrovna zpopelavěla a hryzla se do rtů ostrými zuby; v
štěrbinách očí jí žířil bezmezný odpor či co.
„Nu?“ řekla ostře.
„Panenská, bezcitná, vilná, vzteklá a pyšná, – vypráhlá jako troud, jako troud
– a zlá; vy jste zlá; vy jste palčivá samou krutostí a nenávistná a bez srdce;
vy jste zlá a k prasknutí nabitá náruživostí; nedotknutelná, chtivá, tvrdá,
tvrdá k sobě, led a oheň, oheň a led –“
Princezna mlčky pokývla: ano.
„– k nikomu dobrá, k ničemu dobrá; nadutá, vznětlivá jako lunt, neschopná
milovat, otrávená a planoucí – řeřavá – speklá žárem, a všechno kolem vás
mrzne.“
„Musím být tvrdá k sobě,“ šeptala princezna. „Vy nevíte – vy nevíte –“ Mávla
rukou a vstala. „Děkuju vám. Pošlu vám Paula.“
Vyliv takto svou osobní, uraženou hořkost, začal Prokop o princezně smýšlet
laskavěji; dokonce ho tuze mrzelo, že se mu nyní zřejmě vyhýbá. Chystal se
říci jí při nejbližší příležitosti něco hodně přívětivého, ale příležitost se
už nenaskytla.
Na zámek přijel kníže Rohn, zvaný mon oncle Charles, bratr nebožky kněžny,
takový vzdělaný a jemný světoběžník, amatér všeho možného, tres grand artiste,
jak se říkalo, který dokonce napsal několik historických románů, ale jinak byl
nadmíru milý člověk; k Prokopovi pojal zvláštní náklonnost a trávil u něho
celé hodiny. Prokop mnoho profitoval od jemného pána, obrousil se jaksi a
pochopil, že jsou na světě také jiné věci než destruktivní chemie. Oncle
Charles byl vtělená anekdotická kronika; Prokop rád stočil hovor na princeznu
a naslouchal se zájmem, jaké to bývalo zlé, ztřeštěné, pyšné a velkodušné
děvčátko, jež kdysi střelilo po svém maître de danse a jindy si chtělo dát
vyříznout kus kůže na transplantaci pro popálenou chůvu; když jí to zakázali,
porazila ze vzteku une vitrine s nejvzácnějším sklem. Le bon oncle také
přivlekl k Prokopovi klacka Egona a dával jej (Prokopa) chlapci za příklad s
takovými elóžemi, že se chudák Prokop červenal stejně jako Egonek.
Po pěti nedělích už běhal o holi; vracel se stále častěji do laboratoře a
pracoval jako morovatý, až mu zas procitla bolest v noze, takže cestou domů
zrovna visel na ruce pozorného Holze. Pan Carson zářil, když viděl Prokopa tak
mírného a pracovitého, a ťukal chvílemi na onu stranu, kde v Pánu odpočíval
Krakatit; než to byla věc, o níž Prokop nechtěl ani slyšet.
Jednoho večera bylo na zámku nějaké slavné soirée; nuže, na tento večer
připravil Prokop svůj coup. Princezna zrovna stála ve skupině generálů a
diplomatů, když se otevřely dveře a vešel – bez hole – vzdorovitý vězeň,
poprvé poctívaje knížecí křídlo svou návštěvou. Oncle Charles a Carson mu
běželi vstříc, kdežto princezna na něho jen rychle, zkoumavě pohlédla přes
hlavu čínského vyslance. Prokop si myslel, že ho přijde uvítat; ale když
viděl, že se zastavila s dvěma staršími, až po pupek dekoletovanými paničkami,
zamračil se a couval do kouta, neochotně se ukláněje náramným osobnostem,
kterým ho pan Carson představoval pod titulem „slavného učence“, „našeho
slavného hosta“ a tak dále; jak se zdálo, převzal tu pan Carson roli Holzovu,
neboť nehnul se od Prokopa na krok. Čím dál, tím se Prokop nudil zoufaleji;
vtlačil se už docela do kouta a škaredil na celý svět. Teď mluví princezna s
nějakými arcihodnostáři, jeden z nich je dokonce admirál a druhý veliké
zahraniční zvíře; princezna se kvapně podívá stranou, kde se kaboní Prokop,
ale v tu chvíli k ní přistupuje pretendent jistého zrušeného trůnu a odvádí ji
na opačnou stranu. „Nu, já půjdu domů,“ bručí Prokop a rozhoduje se v hloubi
své černé duše, že do tří dnů udělá nový pokus o útěk. V tom okamžiku stojí
před ním princezna a podává mu ruku. „Jsem ráda, že jste zdráv.“
Prokopa zradila veškera dobrá výchova oncle Charlesa. Udělal masívní pohyb
rameny (míněný jako poklona) a řekl medvědím hlasem. „Myslel jsem, že mne ani
nevidíte.“ Pan Carson zmizel, jako by se propadl.
Princezna je ohromně vystřižena, což uvádí Prokopa ve zmatek; neví, kam se má
dívat, ale vidí její tuhé snědé maso s popraškem pudru a cítí pronikavou vůni.
„Slyšela jsem, že zase pracujete,“ mluví princezna. „Co zrovna děláte?“
„Nu, to a ono,“ plave Prokop, „většinou nic valného.“ Hola, teď je tu
příležitost napravit onu surovost… nu, tehdy ten insult s tou rukou; ale co u
všech všudy lze říci zvláště přívětivého? „Kdybyste chtěla,“ mručí, „udělal
bych… nějaký pokus… s vaším pudrem.“
„Jaký pokus?“
„Třaskavinu. Máte toho na sobě… že by se tím dal vypálit kanón.“
Princezna se zasmála. „Já nevěděla, že pudr je třaskavina!“
„Všecko je třaskavina… když se to vezme pořádně do ruky. Vy sama –“
„Co?“
„Nic. Ztajený výbuch. Vy jste strašně brizantní.“
„Když mne někdo vezme pořádně do ruky,“ zasmála se princezna, a náhle
zvážněla. „Zlá, bezcitná, vzteklá, chtivá a pyšná, že ano?“
„Děvčátko, které se chce nechat stáhnout z kůže… pro starou bábu…“
Princezna se zapálila. „Kdo vám to řekl?“
„Mon oncle Charles,“ pleskl Prokop.
Princezna ztuhla a byla najednou sto mil daleko. „Ah, kníže Rohn,“ opravila ho
suše. „Kníže Rohn mnoho mluví. Těší mne, že jste all right.“ Malé kývnutí
hlavy, a Wille plovala sálem po boku kavalíra v uniformě nechávajíc Prokopa
zuřit v koutě.
Nicméně ráno nato donesl pan Paul Prokopovi cosi jako svátost, a že prý to
přinesla princeznina komorná.
Byla to krabička pronikavě vonného nahnědlého pudru.


XXIX.

Prokopa dráždila a znepokojovala ta silná ženská vůně, když pracoval skloněn
nad krabičkou pudru; bylo mu, jako by sama princezna byla v laboratoři a
nahýbala se nad jeho ramenem.
Ve své mládenecké nevědomosti dříve netušil, že pudr je vlastně jen škrobový
prášek; považoval jej patrně za zemitou barvu. Nuže, škrob je výborná věc
dejme tomu na flegmatizování příliš ofenzívních třaskavin, protože je sám o
sobě netečný a tupý; tím hůře, má-li se sám stát třaskavinou. Nyní si s ním
naprosto nevěděl rady; drtil si čelo v dlaních, hrozně pronásledován
pronikavou vůní princezninou, a neopouštěl laboratoř ani v noci.
Ti, kdo ho měli rádi, přestali za ním chodit, neboť schovával před nimi svou
práci a netrpělivě si na ně vyjížděl pořád mysle na zlořečený pudr. U všech
všudy, co ještě zkusit? Po pěti dnech mu začalo svítat; horečně studoval
aromatické nitroaminy, načež se pustil do syntetické páračky, jakou jakživ
nedělal. A pak jedné noci to leželo před ním, nezměněné ve vzhledu a pronikavě
vonící: hnědavý prášek, z něhož dýše zralá ženská pleť.
Natáhl se na kavalci zmořen únavou. Zdálo se mu, že vidí plakát s nápisem
„Powderit, nejlepší třaskavý poudre na pleť“, a na plakátě je vymalována
princezna a vyplazuje na něj jazyk. Chce se odvrátit, ale z plakátu se vysunou
dvě nahé snědé paže a medúzovitě ho táhnou k sobě. Tu vytáhl z kapsy křivák a
přeřízl je jako salám. Pak se zděsil, že se dopustil vraždy, a prchal ulicí,
ve které před léty bydlel. Stálo tam hrčící auto, i skočil do něho křiče
„jeďte rychle“. Auto se rozjelo, a tu teprve shledal, že u volantu sedí
princezna a má na hlavě koženou přilbici, v níž ji dosud neviděl. V ohybu
cesty někdo se vrhá před auto, patrně aby zastavilo; nelidský řev, kolo se
přehouplo přes cosi měkkého, a Prokop se probudil.
Nahmatal, že má horečku, i vstal a hledal v laboratoři něco léčivého. Nenašel
nic než absolutní alkohol; přihnul si pořádně, spálil si ústa i požerák a šel
znovu lehnout s motající se hlavou. Zdály se mu ještě nějaké vzorce, květiny,
Anči a zmatená jízda vlakem; pak se vše rozplynulo v hlubokém spánku.
Ráno si sehnal povolení podniknout na střelnici pokusnou explozi, z čehož měl
Carson nezřízenou radost. Prokop si zakázal účast jakéhokoliv laboranta a sám
dohlížel, aby pokusná chodba byla vydlabána v pískovém kameni co nejdále od
zámku, v té části střelnice, kde ani nebylo elektrické vedení, takže bylo
nutno přiložit obyčejný doutnák. Když bylo vše připraveno, vzkázal princezně,
že přesně ve čtyři hodiny vyletí do povětří její krabička pudru. Osobně pak
doporučil Carsonovi, aby vyklidil nejbližší baráky a naprosto zamezil
komukoliv přístup v okruhu jednoho kilometru; dále si vyžádal, aby pro
tentokrát byl na čestné slovo zbaven Holze. Pan Carson sice mínil, že je to
příliš hluku pro omeletu, ale celkem vyhověl Prokopovi ve všem.
Před čtvrtou hodinou nesl Prokop vlastnoručně krabičku pudru k výbušné štole,
čichl naposledy s jistou lačností k princeznině vůni a ponořil krabičku do
jámy; tam ji pak podložil rtuťovou kapslí a navázal Bickfordovu šňůru
vyměřenou na pět minut; načež se uvelebil vedle a čekal s hodinkami v ruce, až
budou za pět minut čtyři.
Ahaha, teď jí ukáže, teď ukáže té zpupné slečince, co dovede. Nu, tohle bude
jednou exploze jak se patří, něco jiného než pokusné bouchačky tam na Bílé
hoře, kde se musel ke všemu schovávat před strážníkem; bude to výbuch slavný a
svobodný, ohnivý sloup až k nebi, nádherná síla, veliké udeření hromu;
rozštípnou se nebesa mocí ohňovou, a jiskra vykřísnutá rukou člověka –
Za pět minut čtyři. Prokop rychle zapálil šňůru a upaloval odtud s hodinkami v
ruce, slabě pokulhávaje. Za tři minuty; hrome, teď rychleji. Za dvě minuty. A
tu zahlédl napravo princeznu provázenou panem Carsonem, jak míří k výbušné
jámě. Strnul na okamžik hrůzou a zařval na ně výstrahu; pan Carson se zarazil,
ale princezna ani se neohlížejíc šla dál; Carson klusal za ní, patrně ji
přemlouvaje, aby se vrátila. Přemáhaje prudkou bolest v noze řítil se Prokop
za nimi. „Lehněte,“ ryčel, „u všech čertů lehněte!“ Jeho obličej byl tak
hrozný a zběsilý, že pan Carson zbledl, udělal dva veliké skoky a položil se
do hlubokého příkopu. Princezna šla pořád; nebyla už dále od výbušné jámy než
dvě stě kroků. Prokop praštil hodinkami o zem a pádil za ní. „Lehnout,“ zařval
a chytil ji za rameno. Princezna se prudce obrátila a měřila ho užaslým
pohledem, co si to jako dovoluje; a tu ji Prokop oběma pěstmi srazil na zem a
padl na ni celou svou tíhou.
Tuhé, tenké tělo se zoufale pod ním zazmítalo. „Hade,“ sykl Prokop, a těžce
dýchaje tiskl princeznu vší silou hrudníku k zemi. Tělo pod ním se vzepjalo
obloukem a smýklo sebou stranou; avšak kupodivu, ze sevřených úst
princezniných se nevydral ani hlásek, jen krátce, rychle dýchala v horečném
zápasu. Prokop vtiskl koleno mezi její kolena, aby se mu nevysmekla, a
zacpával jí dlaněmi uši, mysle bleskově na to, že by jí explozí mohly
prasknout bubínky. Ostré nehty se mu zaryly do šíje a v tváři pocítil vzteklé
hryznutí čtyř lasiččích špičáků. „Bestie,“ supěl Prokop a hleděl setřást
zakousnuté zvíře; avšak nepovolila, jako přisátá, a z hrdla se jí vydral
vrkavý zvuk; její tělo se vlnivě vzpínalo a převracelo se jako v křeči. Známá
pronikavá vůně omámila Prokopa; srdce se v něm splašeně rozbouchalo, a v tom
chtěl vyskočit, nemysle už na explozi, jež musí vyletět v nejbližší vteřině.
Tu však cítil, že jektající kolena obemkla a svírají jeho nohu a dvě paže mu
křečovitě opínají hlavu i šíji; a na tváři pocítil vlhký, palčivý, třesoucí se
dotyk úst a jazyka. Zaúpěl hrůzou a hledal svými rty ústa princeznina. Vtom
třeskla strašlivá exploze, sloup hlíny a kamení se vyryl ze země, něco prudce
udeřilo Prokopa do temene, ale ani o tom nevěděl; neboť v tom okamžiku se
zaryl do horoucí vláhy bezdechých úst a líbal rty, jazyk, zuby, ústa otevřená
a vrkající; pružné tělo pod ním rázem ochablo a chvělo se dlouhými vlnami.
Zahlédl nebo se mu jen zdálo, že pan Carson vstal a vykoukl, ale horempádem se
zase položil na zem. Třesoucí se prsty šimrají Prokopovu šíji nesnesitelnou a
divou rozkoší; chraptivá ústa celují jeho tváře a oči drobnými, rozechvěnými
polibky, zatímco Prokop se žíznivě vpíjí do tlukoucí palčivosti vonného hrdla.
„Drahý, drahý,“ lechtá a pálí ho v uše horký, vlhký šepot, jemné prsty se mu
zarývají do vlasů, plihé tělo se napíná a dlouze k němu přilne celou délkou; a
Prokop se přisál k prýštícím rtům nekonečným sténajícím polibkem.
Sss! Odstrčen loktem Prokop vyskočil a mnul si čelo jako opilý. Princezna
usedla a rovnala si vlasy. „Podejte mi ruku,“ kázala suše, kvapně se rozhlédla
a přitiskla honem jeho podanou ruku k planoucí líci; náhle ji prudce
odstrčila, zvedla se, ztuhla a širokýma očima se dívala někam do prázdna.
Prokopovi bylo z ní až úzko, chtěl se k ní vrhnout; trhla nervózně ramenem,
jako by něco shazovala; viděl, že se strašně hryže do rtů. Teprve teď si
vzpomněl na Carsona; našel ho opodál, jak leží na zádech – ale ne už v příkopě
– a vesele mrká k modrému nebi. „Už je po tom?“ spustil leže a zatočil palci
na břiše mlýnek. „Já se totiž hrozně bojím takových věcí. Mám už vstát?“
Vyskočil a otřepal se jako pes. „Báječná exploze,“ povídal nadšen, a jen tak
jakoby nic mrkl po princezně.
Princezna se obrátila; byla olivově bledá, ale kompaktní a zvládnutá. „To bylo
všecko?“ ptala se ledabyle.
„Můj ty kriste,“ repetil Carson, „jako by toho nebylo dost! Propána, jediná
krabička pudru! Člověče, vy jste čaroděj zapsaný ďáblu, král pekel či kdo. Co?
Baže. Král hmoty. Princezno, ejhle král,“ hodil s patrnou narážkou, a už zas
uháněl dále: „Geniální, že? Jedinečný člověk. My jsme jen hadráři, na mou
čest. Jaké jste tomu dal jméno?“
Omámenému Prokopovi se vracela rozvaha. „Ať to princezna pokřtí,“ řekl, rád,
že se na tolik vzmohl. „Je to… její.“
Princezna se zachvěla. „Nazvete to třeba Vicit,“ sykla ostře.
„Co?“ chytil se toho pan Carson. „Aha, Vicit. Znamená ,zvítězil‘, že?
Princezno, vy jste geniální! Vicit! Ohromné, haha! Hurá!“
Než Prokopovi se mihla hlavou etymologie jiná a strašlivá. Vitium. Le vice.
Neřest. Pohlédl s hrůzou na princeznu; ale na její upjaté tváři nebylo lze
čísti žádné odpovědi.


XXX.

Pan Carson běžel napřed k místu výbuchu. Princezna – patrně schválně – se
opozdila; Prokop myslel, že mu chce něco říci, ale ona jen ukázala prstem na
tváři: pozor, tady – Prokop si rychle sáhl na tvář; našel tam krvavé stopy
jejího kousnutí, i zvedl hrst hlíny a rozmazal si ji po líci, jako by ho při
výbuchu zasáhla hrouda.
Výbušná jáma byla vyryta jako kráter v průměru asi pěti metrů; bylo těžko
odhadnouti brizanci, ale Carson páčil výkon na pětinásobek oxiliquitu. Krásná
látečka, mínil, ale pro praktické užití trochu moc silná. Vůbec pan Carson
obstarával celý hovor hravě klouzaje přes povážlivé trhliny konverzace; a když
se na cestě zpátky s poněkud okatou horlivostí poroučel, že prý musí ještě to
a ono, padla na Prokopa ukrutná tíha: o čem mám nyní mluvit? Bůhví proč se mu
zdálo, že se ani slovem nesmí dotknout oné divé a temné události, když nastala
exploze a „nebesa se rozštípla mocí ohňovou“; kvasil v něm hořký a nechutný
pocit, že by ho princezna mrazivě odbyla jako lokaje, se kterým – se kterým –
Zatínal pěstě ošklivostí a přežvykoval cosi naprosto vedlejšího, nejspíš o
koních; slova mu vázla v krku, a princezna zřejmě zrychlovala krok, aby už co
nejdříve byla v zámku. Prokop silně kulhal, ale nedával to znát. V parku se
chtěl poroučet, avšak princezna zabočila na postranní cestu. Následoval ji
váhavě; tu se k němu přimkla ramenem, zvrátila hlavu nazad a nastavila žíznivé
rty.
Princeznin čínský ratlík Toy zavětřil odněkud svou velitelku a piště radostí
letěl k ní přes záhony a křovím. A tu je, haha! ale co to? Ratlík ustrnul: ten
Velký Nevlídný jí cloumá, jsou do sebe zakousnuti, potácejí se v němém a
zuřivém zápase; oho, Paní to projela, ruce jí klesly a leží sténajíc v loktech
Velkého; teď ji zadáví. A Toy začal řváti „pomoc! pomoc!“ ve svém psím nebo
čínském jazyce.
Princezna se vyrvala z náručí Prokopova. „I ten pes, i ten pes,“ zasmála se
nervózně. „Pojďme!“ Prokopovi se motala hlava, byl stěží s to udělat několik
kroků. Princezna se do něho zavěsila (šílená! což kdyby někdo –), vleče jej,
ale nohy jí váznou; zarývá prsty do jeho paže, má chuť drásat či co, syká,
vraští obočí, v očích se jí to temně propadá; a náhle s chraptivým vzlyknutím
letí Prokopovi na krk, až zavrávoral, a hledá jeho ústa. Prokop ji drtí pažemi
i zuby; předlouhé bezdeché sevření, a tělo napjaté jako luk plihne, hroutí se,
poklesá měkce a bezvládně; se zavřenýma očima leží princezna na jeho prsou a
blábolí slabiky sladké a beze smyslu, nechává si plenit tváře i hrdlo prudkými
polibky a vrací je opile a jakoby ani nevědouc o sobě: do vlasů, na ucho, na
ramena, omámená, poddajná, omdlévající, bez konce něžná, pokorná jako onučka a
snad, bože, snad v tuto vteřinu šťastná nějakým nevýslovným a bezbranným
štěstím; ó bože, jaký úsměv, jaký rozechvěný a přesličný úsměv na tiše
srkajících rtech.
Otevřela, vytřeštila oči a prudce se vyvinula z jeho rukou. Stáli na dva kroky
od hlavní aleje. Přejela si obličej dlaněmi jako ten, kdo procitá ze sna;
odstoupila vratce a opřela se čelem o peň dubový. Sotva ji Prokop pustil z
tlap, rozpáčilo se mu srdce ohavnými, ponižujícími pochybnostmi: jsem, kriste,
jsem pro ni sluha, na kterém se… patrně… jen tak rozněcuje ve… v… v nepříčetné
chvíli, kdy… kdy ji přemohla její samota či co; nyní mne odkopne jako psa, aby
jindy zas… někdo jiný… Přistoupil k ní a neurvale jí položil tlapu na rameno.
Obrátila se krotce s plachým, téměř bázlivým a poníženým úsměvem. „Ne ne,“
zašeptala spínajíc ruce, „prosím, již ne –“
Prokopovi pukalo srdce náhlou přemírou něžnosti. „Kdy,“ bručel, „kdy vás zas
uvidím?“
„Zítra, zítra,“ šeptala úzkostně a couvala k zámku. „Musíme jít. Tady nelze –“
„Zítra, kde?“ naléhal Prokop.
„Až zítra,“ opakovala nervózně, zimavě se choulila do sebe a spěchala beze
slova. Před zámkem mu podala ruku: „Sbohem.“
Jejich prsty se palčivě spletly; nevěda o tom táhl ji k sobě. „Nesmíš, teď
nesmíš,“ zasykla a ožehla ho plamenným pohledem.
Jinaké větší škody pokusný výbuch Vicitu nezpůsobil. Shodilo to jen několik
komínů na blízkých barácích a vyrazily se tlakem vzduchu nějaké okenní
tabulky. Také velké vitráže v pokoji knížete Hagena pukly; v tu chvíli se
chromý pán namáhavě vztyčil a stoje, jako voják, očekával další katastrofu.
Společnost v panském křídle seděla po večeři u černé kávy, když vešel Prokop
rovnou hledaje očima princeznu; nemohl už snést řeřavá muka pochybností.
Princezna zbledla; ale žoviální strýček Rohn se hned Prokopa ujal a gratuloval
mu k skvělému výkonu a kdesi cosi. Dokonce nadutý Suwalski se vyptával se
zájmem, je-li to pravda, že pán může každou věc obrátit v třaskavinu. „Dejme
tomu takový cukr,“ opakoval pořád, a žasl, když Prokop zabručel, že cukrem se
střílelo už dávno za Veliké války. Po jistou dobu byl Prokop vůbec středem
zájmu; ale koktal, odbýval všechny otázky a za živého boha nerozuměl
povzbuzujícím pohledům princezniným; jen je chytal svýma krvavýma očima s
děsnou pozorností. Princezna byla jako na trní.
Nu, pak se hovor stočil jinam, a Prokopovi se zdálo, že si ho nikdo nevšímá;
ti lidé si rozuměli tak dobře, mluvili velice lehce, v narážkách a s ohromným
zájmem o věcech, kterým on vůbec nerozuměl nebo na kterých zhola nic neviděl.
I princezna celá ožila; tak vidíš, má tisíckrát víc společného s těmi panáky
než s tebou. Mračil se, nevěděl co s rukama, zavařilo to v něm slepým vztekem;
tu postavil číšku s kávou tak prudce, že se roztříštila.
Princezna upřela na něho hrozné oči; ale šarmantní oncle Charles zachránil
situaci tím, že začal povídat o lodním kapitánovi, který rozmačkal v prstech
pivní láhev. Jakýsi tlustý cousin tvrdil, že by to dovedl také. Tu tedy dali
přinést prázdné pivní láhve, a jeden po druhém za hlučného haló zkoušel,
rozdrtí-li některou z nich. Byly to těžké láhve z černého skla: nepraskla
žádná.
„Teď vy,“ kázala princezna s rychlým pohledem na Prokopa.
„To nesvedu,“ bručel Prokop, ale princezna poškubla obočím tak – tak
velitelsky – – Prokop vstal a popadl láhev kolem hrdla; stál nehnutě,
nekroutil se úsilím jako ti ostatní, jen svalstvo v obličeji mu k prasknutí
nabíhalo; vypadal jako pračlověk, který se chystá někoho zabít krátkým kyjem:
nasupený, s ústy námahou zkřivenými a tváří jakoby přeseknutou hrubými svaly,
s plecí šikmo schýlenou, jako by chtěl zamávat lahví v gorilím útoku, upřel
krví zalité oči na princeznu. Nastalo ticho. Princezna se zvedla s očima
zrovna vzepřenýma do jeho; rty se jí stáhly nad zaťatými zuby, v olivové líci
jí vystoupily šlašité provazce, svraštila obočí a prudce oddychovala jakoby
děsnou tělesnou námahou. Tak stáli proti sobě s očima do sebe zakleslýma a
svraštěnou tváří, jako dva zuřiví zápasníci; konvulsivní záchvěvy probíhaly
souběžně jejich těly od pat až k šíji. Nikdo ani nedýchal; bylo slyšet jen
sípavé chroptění dvou lidí. Tu něco chrustlo, třesklo sklo a spodek láhve
řinkl v střepech na podlahu.
První se vzpamatoval mon oncle Charles; udělal zmatený krok vpravo a vlevo,
ale pak se vrhl k princezně. „Minko, ale Minko,“ zašeptal chvatně a spustil
ji, udýchanou a téměř klesající, do lenošky; klekl před ní a vší silou
rozvíral její křečovitě zaťaté pěstě; měla dlaně plné krve, jak si zaryla
nehty do masa. „Vemte mu tu láhev z ruky,“ kázal honem le bon prince a páčil
princezně prst za prstem.
Princ Suwalski se vzpamatoval. „Bravo,“ zařval a začal hlučně tleskat; ale tu
již von Graun popadl Prokopovu pravici, jež dosud drtila chrastící střepy, a
zrovna vylamoval jeho křečí sevřené prsty. „Vodu,“ křikl, a tlustý cousin,
zmateně něco hledaje, popadl jakousi dečku, polil ji vodou a hodil Prokopovi
na hlavu.
„Ahahah,“ vydralo se z Prokopa úlevou; křeč povolila, ale v hlavě mu ještě
vířil mrtvičný nával krve; a nohy se mu tak třásly slabostí, že se jen svezl
na židli.
Oncle Charles masíroval na koleně zkřivlé, zpocené a třesoucí se prsty
princezniny. „To jsou nebezpečné hry,“ bručel, zatímco princezna, úplně
vyčerpána, stěží popadala dechu; ale na rtech se jí chvěl uchvácený, blouznivě
vítězný úsměv.
„Vy jste mu pomáhala,“ vyhrkl tlustý cousin, „to je to.“
Princezna vstala sotva vlekouc nohy. „Pánové prominou,“ děla chabě, pohlédla
plnýma, zářivýma očima na Prokopa, až se zhrozil, že si toho kdekdo všimne, a
odešla podpírána strýčkem Rohnem.
Nu, pak bylo nutno oslavit nějak Prokopův výkon; koneckonců byli to
dobromyslní mládenci, kteří se jen hrozně rádi chvástali svými hrdinskými
kousky. Prokop u nich ohromně stoupl v ceně tím, že rozmačkal láhev a dovedl
pak vypít neuvěřitelné množství vína a kořalek, aniž spadl pod stůl. Ve tři
hodiny ráno jej princ Suwalski slavnostně líbal a tlustý cousin téměř se
slzami v očích mu nabízel tykání; pak skákali přes židle a dělali strašný
rámus. Prokop se usmíval a měl hlavu jako v oblacích; ale když ho chtěli
dovést k jediné balttinské holce, vytrhl se jim a prohlásil, že jsou opilá
hovada a on že jde spat.
Avšak místo aby tak zcela rozumně učinil, pustil se do černého parku a dlouho,
nesmírně dlouho měřil očima temnou frontu zámku hledaje jakési okno. Pan Holz
dřímal patnáct kroků dále, opřen o strom.


XXXI.

Den nato pršelo. Prokop běhal po parku vztekaje se, že takhle princeznu asi
vůbec neuvidí. Avšak vyběhla prostovlasá do deště a utíkala k němu. „Jen na
pět minut, jen na pět minut,“ šeptala udýchaně a nastavila mu rty k políbení.
Tu však zahlédla pana Holze. „Kdo je ten člověk?“
Prokop se kvapně ohlédl. „Kdo?“ Byl už tak zvyklý na svůj stín, že si ani
neuvědomoval jeho stálou blízkost. „To je… jen můj hlídač, víte?“
Princezna jen obrátila na Holze velitelské oči; pan Holz ihned zastrčil lulku
a uklidil se o kus dál. „Pojď,“ šeptala princezna a vlekla Prokopa k altánu.
Teď tam sedí a netroufají si políbit se; neboť pan Holz mokne někde poblíž
altánu. „Ruku,“ káže potichu princezna a províjí svými horečnými prsty
uzlovité, rozmlácené pahýly Prokopovy. „Milý, milý,“ lichotí se, ale hned
přísně spouští: „Nesmíš se tak na mne dívat před lidmi. Já pak nevím, co
dělám. Počkej, počkej, jednou ti skočím kolem krku a bude ostuda, oh bože!“
Princezna zrovna ustrnula. „Šli jste včera k holkám?“ ptá se najednou.
„Nesmíš, teď jsi můj. Milý, milý, pro mne je to tak těžké – Proč nemluvíš? Jdu
ti říci, abys byl opatrný. Mon oncle Charles už slídí – Včera jsi byl skvělý!“
Mluvil z ní překotný neklid. „Hlídají tě pořád? Všude? I v laboratoři? Ah,
c’est bęte! Když jsi včera rozbil ten šálek, byla bych tě šla políbit. Tak
skvostně jsi se vztekal. Pamatuješ se, jak jsi se tenkrát v noci utrhl z
řetězu? Tehdy jsem šla za tebou jako slepá, jako slepá –“
„Princezno,“ přerušil ji Prokop chraptivě, „něco mně musíte říci. Buď je to…
všecko… rozmar vznešené dámy, nebo…“
Princezna pustila jeho ruku. „Nebo co?“
Prokop k ní stočil zoufalé oči. „Buď si se mnou jenom hrajete –“
„Nebo?“ protáhla se zřejmou rozkoší trýznit ho.
„Nebo mne – do jisté míry –“
„– milujete, ne? Poslyš,“ řekla, založila ruce za hlavou a dívala se na něho
zúženýma očima, „když se mi v jednu chvíli zdálo, že… že jsem se do tebe
zamilovala, víš? opravdu zamilovala, na smrt, jako blázen, tedy tenkrát jsem
se pokusila… zmařit tě.“ Přitom luskla jazykem jako tehdy na Premiera. „Nikdy
bych ti nemohla odpustit, kdybych se do tebe zamilovala.“
„Lžete,“ křikl Prokop rozlícen, „teď lžete! Nesnesl bych… nesnesl bych
pomyšlení, že je to… jenom… flirt. Nejste tak zkažená! Není to pravda!“
„Když to tedy víš,“ řekla princezna tiše a vážně, „pročpak se mne ptáš?“
„Chci to slyšet,“ drtil Prokop, „chci, abys to řekla… přímo… mně řekla, co ti
jsem. To, to chci slyšet!“
Princezna zavrtěla hlavou.
„Musím to vědět,“ zaskřípal Prokop, „jinak – jinak –“
Princezna se mdle usmála a vložila svou ruku na jeho pěsť. „Ne, prosím tě,
nechtěj, nechtěj, abych ti to řekla.“
„Proč?“
„Pak bys měl nade mnou příliš moci,“ děla tichounce, a Prokop se zachvěl
radostí.
Pana Holze venku přepadl zákeřný kašel, a zdáli mihla se mezi keři silueta
strýčka Rohna. „Vidíš, už hledá,“ zašeptala princezna. „Večer k nám nesmíš.“
Ztichli tisknouce si ruce; jenom déšť šelestil na střeše altánu a prochvíval
je rosným chladem. „Milý, milý,“ šeptala princezna a přiblížila se líčkem k
Prokopovi. „Jaký ty jsi? Nosatý, zlostný, celý zježený – Říkají, že jsi velký
učenec. Proč nejsi kníže?“
Prokop sebou trhl.
Otřela se lící o jeho rameno. „Už se zas zlobíš. A mně, mně jsi řekl bestie a
ještě horší věci. Vidíš, ty mi to neosladíš, to, co dělám… a budu dělat…
Milý,“ skončila nehlasně a vztáhla ruku k jeho tváři.
Sklonil se k jejím rtům; chutnaly kajícným steskem.
V šumění deště se blížily kroky pana Holze.
Nemožno, nemožno! po celý den se Prokop trudil a špehoval, kde by ji zahlédl.
„Večer k nám nesmíš.“ Nu ovšem, nejsi z její společnosti; je jí volněji mezi
urozenými klacky. Bylo to prapodivné: v hloubi srdce se Prokop ujišťoval, že
ji vlastně nemá rád, ale žárlil zběsile, umučeně, pln vzteku a pokoření. Večer
se potloukal v dešti po parku a myslel na to, že teď sedí princezna u večeře,
že září, že je tam veselo a volno; připadal si jako prašivý pes vykopnutý do
deště. Nejstrašnější útrapa života je pohana.
A teď tomu udělám konec, rozhodl se; běžel domů, hodil na sebe černé šaty a
vpadl do kuřárny jako včera. Princezna seděla jako nesvá; sotva zahlédla
Prokopa, zabouchalo to v ní a rty jí zvláčněly šťastným úsměvem. Ostatní
mládež ho vítala s kamarádským haló, jen oncle Charles byl o nuanci příliš
zdvořilý. Princezniny oči varovaly: měj se na pozoru! Nemluvila skorem,
zaražená jaksi a nehybná; a přece našla příležitost, aby vtiskla Prokopovi do
ruky zmuchlaný lístek. „Milý, milý,“ bylo tam načmáráno tužkou velikým písmem,
„co jsi to učinil? Odejdi.“ Zmačkal lístek. Ne, princezno, zůstanu tady; dělá
mi tuze dobře pozorovat vaše důvěrné svazky s těmi navoněnými idioty. Za tu
žárlivou paličatost ho princezna odměnila zářivým pohledem; začala si tropit
šašky ze Suwalského, Grauna, všech svých kavalírů, byla zlomyslná, krutá,
impertinentní a vysmívala se jim bez milosti; chvílemi chvatně pohlédla na
Prokopa, ráčí-li být spokojen s touto hekatombou galánů, které mu kladla k
nohoum. Milostpán nebyl spokojen; chmuřil se a žádal očima o pět minut důvěrné
rozmluvy. Tu tedy vstala a vedla ho k nějakému obrazu. „Měj rozum, měj přece
rozum,“ zašeptala horečně, stoupla na špičky a políbila ho vlaze na ono jisté
místo na tváři. Prokop ztuhl leknutím nad touto příšernou ztřeštěností; ale
nikdo to neviděl, dokonce ani oncle Rohn, který jinak vše pozoroval rozumnýma,
smutnýma očima.
Nic víc, nic víc se nestalo toho dne. A přece se Prokop zmítal na svém lůžku
kousaje do podušek; a v druhém křídle zámku nespal někdo po celou noc.
Ráno pan Paul přinesl ostře vonící dopis; neřekl od koho. „Drahý člověče,“
stálo tam, „dnes Tě neuvidím; nevím, co si počnu. Jsme hrozně nápadni; prosím
Tě, buď rozumnější než já. (Několik řádků přeškrtáno.) Nesmíš chodit před
zámkem, nebo za Tebou vyběhnu. Prosím, učiň něco, aby Tě zbavili toho
protivného hlídače. Měla jsem špatnou noc; vypadám hrozně, nechci, abys mne
dnes viděl. Nechoď k nám, mon oncle Charles už dělá narážky; křičela jsem na
něho a nemluvím s ním; mne rozčiluje, že má tak nesnesitelně pravdu. – Milý,
poraď mi: Teď právě jsem vyhnala svou komornou, donesli mi, že má poměr se
štolbou a chodí k němu. Nesnesu to; byla bych ji tloukla do tváře, když se mi
přiznala. Byla krásná a plakala, a já jsem se pásla na tom, jak jí tekou slzy;
představ si, nikdy jsem tak zblízka neviděla, jak se dělá slza, vyhrkne, kane
rychle, zastaví se a pak ji dohoní druhá. Já plakat neumím; když jsem byla
malá, křičela jsem, až jsem zmodrala, ale slzy mi netekly. Vyhnala jsem ji na
hodinu; nenáviděla jsem ji, mrazilo mne, když stála přede mnou. Máš pravdu,
jsem zlá a pukám vztekem; ale proč ona smí všechno? Drahý, prosím tě, přimluv
se za ni; povolám ji zpět a udělám s ní, co budeš chtít, jen když budu vidět,
že dovedeš takové věci ženám odpouštět. Vidíš, jsem zlá a ke všemu ještě
závistivá. Nevím si rady steskem; chtěla bych Tě vidět, ale teď nemohu. Nesmíš
mi psát. Líbám Tě.“
Když toto četl, bouřil v druhém křídle zámku klavír divokými slapy tónů; a
Prokop psal: „Nemilujete mne, to vidím; vymýšlíte si nesmyslné překážky,
nechcete se kompromitovat, omrzelo Vás trýznit člověka, který se Vám
nevnucoval. Rozuměl jsem tomu jinak; stydím se za to a chápu, že chcete
učiniti konec. Nepřijdete-li odpoledne do japonského altánu, dovtípím se toho
dokonale a učiním vše, abych Vás dále neobtěžoval.“
Prokop si oddychl; nebyl zvyklý psát milostné dopisy, a toto se mu zdálo býti
napsáno důkladně a dosti srdečně. Pan Paul to běžel odevzdat; klavír v druhém
křídle uryl, a bylo ticho.
Zatím se Prokop rozběhl za Carsonem; potkal ho u skladů a šel rovnou do toho:
aby ho na čestné slovo nechal chodit bez Holze, a že je ochoten složit
jakoukoliv přísahu, že až do dalšího ohlášení odtud neuteče. Pan Carson se
významně šklebil: ale ano, proč ne? bude chodit volně jako pták, haha, kam
chce a kdy chce, udělá-li totiž jednu maličkost: vydá-li Krakatit. Prokop se
rozzuřil: „Dal jsem vám Vicit, co ještě chcete? Člověče, řekl jsem vám, že
Krakatit nedostanete, ani kdybyste mi hlavu uřízli!“
Pan Carson krčil rameny a litoval, že v takovém případě se nedá nic dělat;
neboť kdo má pod kloboukem Krakatit, je osobnost veřejně nebezpečná,
strašnější než stonásobný vrah a krátce klasický případ pro zajišťovací vazbu.
„Zbavte se Krakatitu, a je to,“ mínil. „Bude vám to stát za to. Jinak… jinak
se bude přemýšlet o tom, dopravit vás někam jinam.“
Prokop, který chtěl užuž spustit válečný křik, se zarazil; zamumlal, že si to
ještě rozmyslí, a běžel domů. Snad je tam odpověď, těšil se; ale nebylo tam
nic.
Odpoledne zahájil Prokop své veliké Čekání v japonském altánu. Až do čtyř
hodin v něm bobtnala nedočkavá, udýchaná naděje: teď, teď už musí každým
okamžikem přijít, princeznička. Ve čtyři už nevydržel sedět; pobíhal po altánu
jako jaguár v kleci, chystal se, že jí obejme kolena, třásl se nadšením a
strachem. Pan Holz diskrétně ustoupil do houští. K páté hodině počal našeho
pána přemáhat ohavný útlak zklamání; tu však jej napadlo: snad přijde až za
šera; to se rozumí, že za šera! Usmíval se a šeptal něžná slova. Za zámkem
zapadá slunce v podzimním zlatě; prořídlé stromy se rýsují ostře a nepohnutě,
je slyšet i šelestění brouka ve spadaném listí; a nežli se nadáš, měkne jasná
hodina zlatovým soumrakem. Na zelené obloze zajiskří večernice; toť klekání ve
vesmíru. Země se ztemňuje pod bledými nebesy, netopýr křivolace zakličkuje,
někde za parkem cinkají potemnělé zvonky stáda; to krávy se vracejí voníce
teplým mlékem. V zámku jedno dvě okna proskočí světlem. Jak, již je šero?
Nebeské hvězdy, málo-li se na vás nahleděl žasnoucí chlapec na mezi
mateřídouškové, málo-li se k vám obracel muž, málo-li trpěl a čekal, a zda
někdy nevzlykl pod svým křížem?
Pan Holz vystoupil ze tmy. „Můžeme jít?“
„Ne.“
Dopít, až do dna dopít své ponížení; neboť, hle, je jisto, že nepřijde. Staniž
se; ale nyní je nutno dopít hořkost, na jejímž dně je jistota; ožrat se
bolestí; navalit, navršit utrpení a hanbu, aby ses svíjel jako červ a zpitoměl
mukou. Chvěl jsi se před štěstím; oddej se bolesti, neboť ona je narkotikum
trpícího. Je noc, již noc; a ona nepřichází.
Strašná radost prošlehla srdcem Prokopovým: Ona ví, že tu čekám (neboť musí to
vědět); vykrade se v noci, až vše bude spát, a poletí ke mně s náručí
otevřenou a ústy plnými mízy polibků; semknem se němi a nepromluvíme, pijíce
si ze rtů nevýslovná doznání. A ona přijde, bledá i potmě, chvějící se
mrazivou hrůzou radosti, a podá mi své hořké rty; a ona vystoupí z černočerné
noci –
Na zámku zhasínají.
Pan Holz trčí přímo před altánem s rukama v kapsách. Jeho unavený obrys praví
„už by toho mohlo být dost“. Ale ten, který v altánu se zběsilým, nenávistným
smíchem udupává poslední jiskřičku naděje, protahuje čas o zoufalé minuty;
neboť poslední minuta čekání bude znamenat Konec Všemu.
V dalekém městečku bije půlnoc. Tedy konec všemu.
Černým parkem uhání Prokop domů, bůhví proč teď má tak naspěch. Běží schýlen,
a pět kroků za ním zívá a kluše pan Holz.


XXXII.

Konec všemu: byla to skoro úleva, nebo alespoň něco jistého a bez pochyb; a
Prokop se do toho zahryzl s buldočí houževnatostí. Dobrá, je konec, není se
již tedy čeho bát. Nepřišla schválně; stačí, tenhle políček stačí; je tedy
konec. Seděl v lenošce neschopen vstát, znovu a znovu se opíjeje svým
ponížením. Odkopnutý sluha. Nestoudná, nadutá, bez citu. Jistě mne dávala k
lepšímu svým galánům. Nuže, dohráno; tím lépe.
Při každém kroku na chodbě zvedl Prokop hlavu v nepřiznaném a horečném
očekávání: snad nesou dopis – Ne, nic. Ani za to jí nestojím, aby se omluvila.
Je konec.
Pan Paul se desetkrát přišourá s ustaranou otázkou v bledých očích: Poroučí
pán něco? Ne, Paule, docela nic. „Počkat, nemáte pro mne nějaký dopis?“ Pan
Paul vrtí hlavou. „Dobře, můžete jít.“
Ledový hrot kamení v Prokopových prsou. Tahle prázdnota, to je konec. I kdyby
dveře se otevřely a stála v nich ona sama, řekl bych: konec. „Milý, milý,“
slyší ji Prokop šeptati, a tu propuká v zoufalství: „Proč jste mne tak
ponížila? Kdybyste byla komorná, odpustil bych vám vaši zpupnost; ale
princezně se neodpouští. Slyšíte? Je konec, konec!“
Pan Paul vrazil do dveří: „Poroučí pán něco?“
Prokop se zarazil; poslední slova opravdu křičel nahlas. „Ne, Paule. Nemáte
pro mne nějaký dopis?“
Pan Paul vrtí hlavou.
Den houstne jako ošklivá pavučina, je už večer. Tu šeptají na chodbě nějaké
hlasy, a pan Paul se přišoupe v radostném spěchu: „Dopis, tady je dopis,“
šeptá vítězně, „mám rozsvítit?“
„Ne.“ Prokop mačká v prstech tenkou obálku a čichá její známou pronikavou
vůni: jako by chtěl poznat čichem, co je uvnitř. Ledový hrot v jeho srdci se
zaryl hlouběji. Proč píše až večer? Protože mi jen poroučí: nesmíte k nám
přijít, a je to. Dobrá, princezno, staniž se; když konec, tedy konec. Prokop
vyskočil, našel potmě čistou obálku a zalepil do ní její dopis neotevřený.
„Paul, Paul! doneste to ihned Její Jasnosti.“
Sotva se Paul vytratil, chtěl ho zavolat zpátky; ale bylo již pozdě; a Prokop
si zdrcen uvědomil, že to, co právě učinil, je bez návratu Konec Všemu. Tu
vrhl se na postel duse v poduškách cosi, co se mu nezvládnutelně dralo z úst.
Přišel pan Krafft, nejspíše zalarmován Paulem, a namáhal se čímkoliv utěšit
nebo rozptýlit palčivě rozrytého člověka. Prokop kázal přinést whisky, pil a
násilně se rozjařil; Krafft cucal sodovku a přisvědčoval mu ke všemu, ačkoliv
to byly věci naprosto nesrovnatelné s jeho zrzavým idealismem. Prokop klnul,
rouhal se, válel se v surových a nejnižších výrazech; jako by mu dělalo dobře
pokálet vše, poplivat a pošlapat a zneuctít. Vyvrhoval ze sebe celé balvany
kleteb a ohavností; překypoval oplzlostmi, tahal z žen zrovna vnitřnosti a
častoval je nejstrašnějšími věcmi, jaké lze vůbec vyslovit. Pan Krafft potě se
hrůzou mlčky přisvědčoval rozlícenému géniu; ale i Prokop se vydal ze své
vehemence, umlkl, chmuřil se a pil, až toho bylo dost; pak ulehl oblečen do
postele hourající se jako loď a hleděl vytřeštěně do vířící tmy.
Ráno vstal rozklížený a zhnusený a odstěhoval se nadobro do laboratoře.
Nedělal nic, jen coural po světnici a kopal před sebou mycí houbu. Pak ho něco
napadlo: namíchal strašnou a labilní třaskavinu a poslal ji na ředitelství,
doufaje, že z toho povstane nějaká lepší katastrofa. Nestalo se nic; Prokop se
vrhl na kavalec a spal nepřetržitě třicet šest hodin.
Probudil se jako jiný člověk: ledový, střízlivý, ztuhlý; bylo mu jaksi na smrt
jedno, co se dálo předtím. Začal zas pracovat tvrdošíjně a metodicky na
explozívních rozpadech atomů; teoreticky docházel k tak strašlivým vyčísleným
brizancím, že mu vstávaly vlasy nad úžasností sil, mezi nimiž žijeme.
Jednou uprostřed počítání jej stísnil letmý nepokoj. Jsem asi unaven, řekl si,
a šel bez klobouku trochu na vzduch. Ani o tom nevěda zamířil k zámku;
mechanicky vyběhl po schodech a putoval chodbou k svému bývalému
„kavalírskému“ pokoji. Paul nebyl na své obvyklé židli. Prokop vešel dovnitř.
Vše bylo, jak to opustil; ale ve vzduchu vála známá, silná vůně princezny.
Nesmysl, mínil Prokop, nějaká sugesce či co; čichal jsem příliš dlouho ostré
zápachy laboratoře. A přece ho to trýznivě rozčilovalo.
Usedl na chvíli a divil se: jak už je to vše daleko. Bylo ticho, odpolední
ticho v zámku; zdalipak se tu něco změnilo? Slyšel tlumené kroky na chodbě,
snad je to Paul; i vyšel ven. Byla to princezna.
Překvapení a skoro hrůza ji vrhly ke stěně; teď tu stojí zsinalá, oči takhle
široké, a rty se jí křiví jako v bolesti, až je vidět korálové maso jejích
dásní. Co hledá v hostinském křídle? Jde asi k Suwalskému, napadlo Prokopa
zčistajasna, a něco se v něm utrhlo; udělal krok, jako by se na ni chtěl
vrhnout, ale vydal jen hrdelní zařičení a ubíhal ven. Byly to ruce, co se za
ním vztáhlo? Nesmíš se ohlédnout! A pryč, pryč odtud!
Až daleko za zámkem, na úhorové půdě střelnice, zaryl Prokop tvář do hlíny a
kamení. Neboť jediné jest horší než bolest ponížení: muka nenávisti.
Deset kroků stranou seděl vážný a soustředěný pan Holz.
Noc, která nastala, byla dusná a tíživá, neobyčejně černá; chýlilo se k bouři.
V takové chvíli jsou lidé divně podrážděni a nemají se nijak rozhodovat o svém
osudu; neboť nedobrý je to čas.
K jedenácté vyrazil Prokop ze dveří laboratoře a omráčil židlí dřímajícího
pana Holze natolik, že mu unikl a zmizel v noční tmě. Chvíli nato padly dva
výstřely u závodního nádraží. Nízko na horizontě se ošklivě blýskalo; pak bylo
tím černěji. Ale z vysoké hráze u vchodu vyletěl ostrý pruh siného světla a
posunoval se kolem nádraží; zachycoval vagóny, rampy, hromady uhlí, a nyní
popadl černou postavu, která uhání, kličkuje, padá k zemi a opět mizí ve
stínu. Nyní utíká mezi baráky k parku; několik postav se vrhá za ní. Reflektor
se otáčí k zámku; opět dva poplašné výstřely, a běžící postava se zarývá do
houští.
Krátce nato zadrnčelo okno ložnice princezniny; princezna vyskočila a
otevřela, a tu vletěl dovnitř zmuchlaný list papíru zatížený kamínkem. Na
jedné straně bylo něco naprosto nečitelně naškrabáno přelomenou tužkou; na
druhé straně byly hustě a drobně psané výpočty. Princezna na sebe házela šaty,
ale tu již padl výstřel za rybníkem; podle zvuku to bylo naostro. Ztuhlými
prsty princezna zadrhovala háčky šatů, zatímco komorná, potřeštěná koza, se
třásla pod peřinou strachy ze střílení. Ale než mohla princezna vyjít, viděla
oknem, jak dva vojáci vlekou někoho černého; burácel jako lev a hleděl je
setřást; nebyl tedy raněn.
Jen na obzoru se blýská širokými žlutými plameny; ale ulevující bouře se ještě
nespustila.
Vystřízlivělý Prokop se vrhl střemhlav do laboratorní práce, nebo aspoň se k
tomu nutil. Před chvílí odešel od něho Carson; byl studeně popuzen a prohlásil
zřetelně, že podle všeho bude pan Prokop co nejdříve transferován jinam, na
místo bezpečnější; když prý to nejde po dobrém, tedy to půjde po zlém. Nu, vše
jedno; na ničem už nezáleží. Zkumavka praskla Prokopovi v prstech.
V předsíni odpočívá pan Holz s hlavou ofáčovanou. Prokop mu strkal pár tisíc
bolestného, nevzal je. Ach co, ať dělá, co chce. Být transferován jinam –
Staniž se. Zlořečené zkumavky! praská jedna po druhé –
V předsíni šramot, jako když někdo vyletí z dřímoty. Zas asi návštěva, Krafft
či kdo; Prokop se ani neobrátil od kahanu, když zaskřípěly dveře. „Milý,
milý,“ zašeptalo to ode dveří. Prokop zavrávoral, zachytil se stolu a otočil
se jako v snách. Princezna stála opřena o veřeje, bledá, s očima temně
utkvělýma, a tiskla pěstě k prsoum, snad aby přemohla tlučení srdce.
Šel k ní chvěje se na celém těle, a dotkl se prsty jejích lící a ramen, jako
by nemohl uvěřit, že je to ona. Položila mu studené, třesoucí se prsty na
ústa. Tu vytrhl dveře a nahlédl do předsíně. Pan Holz zmizel.


XXXIII.

Seděla na kavalci jako ztuhlá, s koleny až pod bradou, změtené vlasy proudem
vrženy přes tvář a ruce stočeny kolem šíje jako v křeči. Hroze se toho, co
učinil, páčil jí hlavu nazad, líbal kolena, ruce, vlasy, smýkal se po zemi,
drtil prosby a laskání; neviděla a neslyšela. Zdálo se mu, že se otřásá
odporem při každém jeho doteku; vlasy se mu lepily k čelu potem úzkosti, i
běžel k hydrantu a pustil si na hlavu proud studené vody.
Potichu vyskočila a přiblížila se k zrcadlu. Šel k ní po špičkách chtěje ji
překvapit; ale tu viděl v zrcadle, jak měří sebe samu s výrazem tak divoké,
hrůzné, zoufalé ošklivosti, že ustrnul. Zahlédla ho za sebou a vrhla se k
němu. „Nejsem ošklivá? Nehnusím se ti? Co jsem udělala, co jsem udělala!“
Přilnula lící k jeho prsoum, jako by se chtěla ukrýt. „Jsem hloupá, viď? Já
vím… já vím, že jsi zklamán. Ale nesmíš mnou pohrdat, víš?“ Zarývala se do
něho tváří jako kající děvčátko. „Že, již neutečeš? Já udělám všechno, nauč
mne všemu, co chceš, víš? jako bych byla tvá žena. Milý, milý, nenechávej mne
teď myslet; budu zas protivná, když budu myslet, budu jako zkamenělá; nemáš
ponětí, nač myslím. Ne, nenech mne teď –“ Zaryla rozechvělé prsty do jeho
šíje; zvedl jí hlavu a líbal ji mumlaje nadšením vše možné. Zrůžověla nyní a
zkrásněla. „Nejsem ošklivá?“ šeptala mezi polibky šťastná a omámená. „Chtěla
bych být krásná jen pro tebe. Víš, proč jsem přišla? Čekala jsem, že mne
zabiješ.“
„A kdybys ty,“ šeptal Prokop kolébaje ji v náruči, „kdybys tušila to… to, co
se stalo, byla bys přišla?“
Princezna kývla hlavou. „Jsem hrozná, viď? Co si tak o mně myslíš! Ale já tě
nenechám myslet.“ Prudce ji sevřel a zvedl. „Ne, ne,“ prosila a bránila se mu;
ale pak spočívala s očima vlaze tonoucíma a sladkými prsty se probírala v
čupřině jeho těžké lbi. „Milý, milý,“ dýchala mu vlhce do tváře, „jak jsi mne
trápil ty poslední dny! Máš mne –?“ Slovo „rád“ neřekla. Horlivě přisvědčil:
„A ty?“
„Mám. Už bys to mohl vědět. Víš, jaký jsi? Jsi nejkrásnější nosatý a šeredný
člověk. Máš krvavé oči jako bernardýn. To je od práce? Snad bys nebyl tak
milý, kdybys byl kníže. Ach, pusť už!“
Vyvinula se mu a šla se k zrcadlu česat. Dívala se tam zkoumavě a pak provedla
před zrcadlem hlubokou dvorskou poklonu. „To je princezna,“ řekla ukazujíc na
svůj obraz, „a tohle,“ dodala bezbarvě a obrátila prst k svým prsoum, „je jen
tvá holka. Tak vidíš. Snad sis nemyslel, že máš princeznu?“
Prokop sebou trhl jako udeřen. „Co to znamená?“ vyhrkl a uhodil pěstí do
stolu, až zařinčelo rozbité sklo.
„To si musíš vybrat, buď princeznu, nebo holku. Princeznu ty nemůžeš mít;
můžeš ji zbožňovat zdálky, ale ruky jí nepolíbíš; a nebudeš se ptát jejích
očí, má-li tě ráda. Princezna nesmí; má za sebou tisíc let čisté krve. Nevíš,
že jsme bývali suverény? Ach, ty nevíš nic; ale musíš vědět aspoň to, že
princezna je na skleněné hoře; tam se nedostaneš. Ale obyčejnou ženskou, tuhle
ordinární hnědou holku můžeš mít; sáhni, je tvá, jako nějaká věc. Nu, tak si
vyber, co z toho dvojího chceš.“
Prokopa z ní zrovna mrazilo. „Princeznu,“ vypravil těžce ze sebe.
Přistoupila k němu a vážně ho políbila na tvář. „Jsi můj, viď? Ty milý! Tak
vidíš, máš princeznu. Tedy přece jsi pyšný na to, že to je princezna? Vidíš,
jak strašnou věc musí princezna udělat, aby se někdo pár dní nadýmal! Pár dní,
pár týdnů; princezna ani nemůže žádat, aby to bylo navždycky. Já vím, já to
vím: od první chvíle, co jsi mne viděl, jsi chtěl princeznu; ze vzteku, z
mužského velikášství nebo proč, viď? Proto jsi mne tak nenáviděl, že jsi mne
chtěl; a já jsem ti naběhla. Myslíš, že mne to mrzí? Naopak, já jsem na to
pyšná, že jsem to provedla. Je to veliký kousek, že? tak se horempádem
zahodit; být princezna, být panna, a přijít… přijít sama…“
Prokop se děsil jejích řečí. „Mlč,“ prosil a vzal ji do chvějících se rukou.
„Nejsem-li vám… roven… rodem…“
„Jak jsi to řekl? Roven? Copak si myslíš, kdybys byl kníže, že bych k tobě
přišla? Oh, kdybys chtěl, abych s tebou jednala jako se sobě rovným, nemohla
bych… být u tebe… takhle,“ vykřikla rozpínajíc nahé paže. „To je ten hrozný
rozdíl, chápeš to?“
Prokopovi klesly ruce. „Tohle jste neměla říkat,“ zaskřípěl couvaje.
Vrhla se mu kolem krku. „Milý, milý, nenechávej mne mluvit! Copak ti něco
vyčítám? Přišla jsem… sama… protožes chtěl utéci nebo se dát zabít, já nevím;
to by přec každé děvče… Myslíš, že jsem to neměla udělat? Řekni! Udělala jsem
špatně? – – Vidíš,“ zašeptala trnouc, „vidíš, ty to také nevíš!“
„Počkej,“ křikl Prokop, vyvinul se jí a velkými kroky měřil pokoj; náhlá
naděje ho zrovna oslňovala. „Věříš ve mne? Věříš, že něco dovedu? Umím
strašlivě pracovat. Nikdy jsem nemyslel na slávu; ale kdybys chtěla… Pracoval
bych ze všech sil! Víš, že… Darwina nesli k hrobu vévodové? Kdybys chtěla,
udělal bych… udělal bych ohromné věci. Umím pracovat – Mohu změnit povrch
země. Nech mi deset let, a uvidíš, uvidíš –“
Zdálo se, že ho ani neposlouchá. „Kdybys byl kníže, stačilo by ti, abych se na
tebe podívala, abych ti ruku podala, a věděl bys, věřil bys, nemusel
pochybovat – Nemuselo by se ti dokazovat… tak hrozně jako já, víš? Deset let!
Dovedl bys mně věřit deset dní? Kdežpak deset dní! Za deset minut ti bude to
všecko málo; za deset minut se budeš mračit, ty milý, a vztekat se, že
princezna tě už nechce… protože to je princezna a ty nejsi kníže, viď? A tož
dokazuj, ty ztřeštěná, ubohá, přesvědči ho, můžeš-li; žádný tvůj důkaz není
dost veliký, žádné ponížení dost nelidské – Běhej za ním, nabízej se, dělej
víc než každá jiná holka, já nevím co, já nevím už co! Co si mám s tebou
počít?“ Přistoupila k němu a nabídla mu rty. „Tak co, budeš mi věřit deset
let?“
Popadl ji drsně vzlykaje. „Už je to tak,“ šeptala a hladila mu vlasy. „Také
sebou trháš na řetěze, viď? A přece bych neměnila… neměnila s tím, jaká jsem
byla. Milý, milý, já vím, že ty mne opustíš.“ Zlomila se mu v rukou; zvedl ji
a rozrýval násilnými polibky její semknutá ústa.
Odpočívala s očima zavřenýma, sotva dýchajíc; a Prokop, nakloněn nad ní, se
srdcem stísněným zkoumal nevyzpytatelný mír té palčivé, napjaté tváře. Vytrhla
se mu jako ze sna. „Co tu všechno máš v těch lahvích? Je to jedovaté?“
Prohlížela jeho regály a nástroje. „Dej mi nějaký jed.“
„Proč?“
„Kdyby mne odtud chtěli odvézt.“
Znepokojil se její vážnou lící, a aby ji podvedl, odměřoval do malé dózičky
plavenou křídu; než vtom již padla sama na krystalinický arzenik. „Neber to,“
křikl, ale už s tím byla v kabelce.
„Tak ty můžeš být slavný,“ vydechla. „Vidíš, na to jsem ani nemyslela.
Povídáš, že Darwina nesli vévodové? Kteří to byli?“
„Nu, na tom snad nezáleží.“
Políbila ho na tvář. „Ty jsi milý! Jakpak by na tom nezáleželo?“
„Tak tedy… vévoda z Argyllu a… a vévoda z Devonshiru,“ bručel.
„Skutečně!“ Přemýšlela o tom, až vraštila čelo. „To bych nikdy neřekla, že
učenci jsou tak… A tys mi to řekl jen tak vedle, jdi!“ Sáhla mu na prsa a
ramena, jako by byl novou věcí. „A ty, ty bys také mohl –? Jistě?“
„Nu, počkej na můj pohřeb.“
„Ach, kdyby to bylo hodně brzo,“ děla roztržitě a s naivní krutostí. „Ty bys
byl hrozně krásný, kdybys byl slavný. Víš, co se mi na tobě nejvíc líbí?“
„Nevím.“
„Já také ne,“ řekla zamyšleně a vracela se k němu s polibkem. „Teď už to
nevím. Teď, kdybys byl kdo chtěl a jaký chtěl –“ Udělala bezmocný pohyb
rameny. „To je prostě provždycky, víš?“
Prokop žasl nad touto monogamní přísností. Stála před ním, až po oči zahalená
v modré lišce, a dívala se na něho třpytivýma, měkkýma očima v hodince
soumraku. „Oh,“ vzdychla náhle a svezla se na kraj židle, „třesou se mi nohy.“
Hladila a třela je s naivní nestoudností. „Jak budu potom jezdit? Přijď, milý,
přijď se mi dnes ukázat. Mon oncle Charles tu dnes není, a i kdyby – Mně už je
to jedno.“ Vstala a políbila ho. „Sbohem.“
Ve dveřích stanula, zaváhala a vrátila se k němu. „Zab mne, prosím tě,“ děla s
rukama svislýma, „zab mne!“
Přitáhl ji dlaněmi: „Proč?“
„Abych nemusela odtud… a abych už nikdy, nikdy už nemusela sem.“
Zašeptal jí do ucha: „… Zítra?“
Pohlédla na něho, a sklonila trpně hlavu; bylo to… přece jen přitakání.
Vyšel až dlouho po ní do vlčího soumraku. Někdo se sto kroků dále zvedl se
země a čistil si rukávem šaty. Mlčelivý pan Holz.


XXXIV.

Když přišel po večeři, nevěřící už a celý ve střehu, stěží ji poznal, jak byla
krásná. Cítila jeho užaslý a žárlivý pohled, pohled, který ji obléval od hlavy
k patě; i zazářila a oddávala se mu očima tak bez ohledu k ostatním, že trnul.
Byl tam nějaký nový host, ďHémon se jmenoval, diplomat či co: člověk
mongolského typu s fialovými pysky a krátkými černými vousy kolem. Tenhle pán
tedy byl patrně znalý fyzikální chemie; Becquerel, Planck, Niels Bohr,
Millikan a taková jména mu jen lítala od huby; znal Prokopa z literatury a
ohromně se interesoval o jeho práci. Prokop se dal strhnout, rozpovídal se,
zapomněl na okamžik dívat se na princeznu; za to utržil pod stolem takové
kopnutí do bérce, že sykl a byl by jí to málem vrátil; nádavkem dostal
planoucí pohled žárlivosti. V té chvíli musel zodpovědět hloupou otázku prince
Suwalského, co je to vlastně ta energie, o které tu pořád povídají; i popadl
cukřenku, vrhl na princeznu pohled tak rozhořčený, jako by jí to chtěl hodit
na hlavu, a vysvětloval, kdyby se podařilo naráz vyvinout a vybít všecku
energii obsaženou v tomhle, že by to vyhodilo do povětří Montblank i s
Chamonix; ale to že se nepodaří.
„Vy to uděláte,“ prohlásil ďHémon určitě a vážně.
Princezna se celým tělem naklonila přes stůl: „Co jste to řekl?“
„Že on to udělá,“ opakoval pan ďHémon s naprostou jistotou.
„Tak vidíš,“ řekla princezna docela nahlas, a vítězně si sedla. Prokop zrudl a
netroufal si na ni pohlédnout.
„A když to udělá,“ ptala se dychtivě, „bude hrozně slavný? Jako Darwin?“
„Když to udělá,“ děl pan ďHémon bez váhání, „budou si králové pokládat za čest
nést cíp jeho pohřební pokrývky. Budou-li ještě nějací králové.“
„Nesmysl,“ bručel Prokop, ale princezna zaplála nevýslovným štěstím. Za nic na
světě by na ni nepohlédl; brumlal celý rudý a v rozpacích drtil mezi prsty
kostky cukru. Konečně se odvážil zvednout oči; dívala se na něj přímo a plně,
se strašlivou láskou. „Máš?“ hodila mu polohlasně přes stůl. Rozuměl předobře:
máš mne rád? – ale dělal, jako by neslyšel, a začal se honem dívat na ubrus.
Proboha, to děvče šílí, nebo chce naschvál –“Máš?“ doletělo přes stůl
hlasitěji a naléhavěji. Kývl rychle a podíval se na ni očima opilýma radostí.
Naštěstí v obecném hovoru to všichni přeslechli; jenom pan ďHémon měl výraz
příliš diskrétní a nepřítomný.
Hovor se stočil sem tam, a najednou pan ďHémon, člověk patrně znalý všeho,
vykládal von Graunovi jeho rodokmen do třináctého století. Princezna se do
toho vmísila s nesmírným zájmem; a tu nový host vypočítával její předky, jako
když bičem mrská. „Dost,“ zvolala princezna, když došel k roku 1007, kdy prvý
Hagen založil pečorský baronát v Estonsku, kohosi tam zavraždiv; neboť dále se
genealogové ovšem nedostali. Ale pan ďHémon pokračoval: tento Hagen čili Agn
Jednoruký byl dokázatelně tatarský kníže, zajatý při nájezdu na Kamskou
oblast; perská historie ví o chánu Aganovi, který byl syn Giw-khana, krále
Turkmenů, Uzbeků, Sartů a Kirgizů, který byl syn Weiwuše, který byl syn Litaj-
khana Dobyvatele. Tento „císař“ Li-Taj je dokázán v čínských pramenech jako
vládce Turkmenska, Dzungarska, Altaje a západního Tibetu až po Kašgar, jejž
spálil povraždiv na padesát tisíc lidí, mezi nimi čínského vladaře, kterému
dal utahovat namočený provaz kolem hlavy tak dlouho, až praskla jako ořech. O
dalších předcích Litajových není nic známo, pokud snad nebudou přístupny
archívy ve Lhase. Jeho syn Weiwuš, i na mongolské pojmy trochu divoký, byl v
Kara Butaku umlácen stanovými tyčemi. Jeho syn Giw-khan vyplenil Chivu a řádil
až po Itil čili Astrachan, kde se proslavil tím, že dal dvěma tisícům lidí
vyloupat oči, uvázal je na provaz a vyhnal do kubánských stepí. Agan-khan
pokračoval v jeho stopách čině nájezdy až po Bolgar neboli dnešní Simbirsk,
kde někde byl zajat, uťata mu pravá ruka a držán jako rukojmí až do té doby,
kdy se mu povedlo utéci k Baltu mezi tamní čudské Livy. Tam byl pokřtěn od
německého biskupa Gotilly nebo Gutilly a nejspíš z náboženského roznícení
zapíchl ve Verro na hřbitově šestnáctiletého dědice pečorského, načež si vzal
jeho sestru za ženu; dokázanou bigamií pak zaokrouhlil své panství až po
jezero Pejpus. Viz o tom letopisy Nikiforovy, kde již je nazýván „knjaz Agen“,
kdežto öselský zápis jej tituluje „rex Aagen“. Jeho potomci, dokončil pan
ďHémon tiše, byli vypuzeni, ale nikoliv sesazeni; načež vstal, uklonil se a
zůstal stát.
Nemůžete si představit, jakou tohle udělalo senzaci. Princezna přímo pila
každé slovo ďHémonovo, jako by ta řada tatarských hrdlořezů byla tím
nejohromnějším zjevením světa; Prokop na ni hleděl s úžasem: ani nemrkla při
dvou tisících párech vyloupaných očí; mimovolně hledal na její tváři tatarské
rysy. Byla překrásná, vytáhla se jaksi a skoro veličensky se uzavřela v sebe;
najednou byla taková distance mezi ní a všemi ostatními, že všichni se
narovnali jako na dvorním dîner a již se ani nepohnuli s očima upřenýma k ní.
Prokop měl tisíc chutí praštit do stolu, říci něco hrubého, porušit tu
strnulou a jaksi bezradnou scénu. Seděla s očima sklopenýma, jako by na něco
čekala, a na jejím hladkém čele kmitlo cosi jako netrpělivost: nu tak, bude
to? Pánové pohlédli tázavě na sebe, na vztyčeného pana ďHémona, a počali jeden
po druhém vstávat. Prokop se zvedl také, nechápaje, oč jde. U všech všudy, co
to znamená? všichni stojí jako svíce s rukama na švech kalhot a hledí k
princezně; teprve teď zvedá princezna oči a kyne hlavou jako ten, kdo děkuje
na pozdrav nebo dává svolení usednout. Skutečně všichni usedají; a teprve když
zase seděl, pochopil Prokop s úžasem: tohle byl hold panovnicí osobě. Naráz se
zapotil trapným vztekem. Kriste, a tu komedii jsem dělal s sebou! Což je to
vůbec možno, což se nedají do smíchu povedené legraci, což je myslitelno, aby
někdo bral vážně takové tatrmanství?
Už nabíral do plic homérický smích, aby spustil hned s prvními (proboha, je to
přec jenom pro švandu?), když se princezna zvedla. Všichni rázem vstali, i
Prokop, pevně přesvědčený, že teď to praskne. Rozhlédla se a utkvěla očima na
tlustém cousinovi; pokročil dva tři kroky k ní, ruce svisle dolů, trochu
nakloněn vpřed, děsně směšný; bohudík, je to tedy přece jen švanda. Princezna
s ním chvíli hovoří a kyne hlavou; tlustý cousin se klaní a pozpátku couvá.
Princezna pohlédne na Suwalského; princ se blíží, odpovídá, poví nějaký uctivý
vtip; princezna se zasměje a kyne hlavou. Což je to tedy vážné? Nyní utkvívá
princezna očima lehce na Prokopovi; ale Prokop se nehnul. Pánové se staví na
špičky a hledí napjatě k němu. Princezna mu dává očima znamení; nehnul se.
Princezna míří k starému, jednorukému majoru od artilerie, který je pokryt
medailemi jako Kybelé cecíky. Major se už narovnává, až to na něm řinčí, ale
tu malým půlobratem stojí princezna těsně u Prokopa.
„Milý, milý,“ povídá tiše a jasně, „máš –? Už se zas mračíš. Já bych tě chtěla
políbit.“
„Princezno,“ bručel Prokop, „co znamená tahle fraška?“
„Nekřič tak. To je vážnější, než si můžeš představit. Víš, že mne teď budou
chtít vdát?“ Zachvěla se hrůzou. „Milý, ztrať se teď odtud. Jdi z chodby do
třetího pokoje a počkej tam na mne. Musím tě vidět.“
„Poslyšte,“ chtěl Prokop něco říci, ale tu již kynula hlavou a mířila plavně k
starému majorovi.
Prokop nevěřil svým očím. Dějí se takovéhle věci, není to smluvená produkce
pro smích, berou ti lidé vážně své role? Tlustý cousin jej vzal pod paží a
táhl diskrétně stranou. „Víte, co to znamená?“ šeptal rozčilen. „Starého
Hagena raní mrtvice, až se to dozví. Panovnický rod! Viděl jste tady ondyno
toho následníka? Měla být svatba a rozešlo se to. Ten člověk, ten je sem jistě
poslán – Jezus, taková linie!“
Prokop se mu vymknul. „Odpusťte,“ mumlal, vysoukal se co možná nejneobratněji
na chodbu a vešel do třetího pokoje. Bylo to jakési čajové zákoutí se
zastřenými světly, samy lak, červený porcelán, kakemona a podobné hlouposti.
Prokop pobíhal s rukama na zádech a bručel v miniaturním pokojíku jako moucha
masařka narážející hlavou na okenní tabule. Sakra, něco se změnilo; pro pár
všivých tatarských rasů, za které by se slušný člověk styděl… Pěkný původ,
děkuju nechci! A pro pár takových Hunů ti idioti zrovna trnou, padají na
břicho, a ona, ona sama – Moucha masařka se bezdeše zarazila. Teď přijde…
tatarská kněžna a řekne: Milý, milý, je mezi námi konec; považ přece, že
pravnučka Litaj-chána se nemůže milovat se synem ševcovým. Klep, klep, slyšel
v hlavě tatínkovo kladívko, a zdálo se mu, že čichá těžký, tříslový pach kůže
a trapné čpění ševcovského mazu; a maminka v modré zástěře stojí, chudák, celá
rudá nad plotýnkou –
Moucha masařka divoce zabzučela. To se ví, kněžna! Kam, kam jsi dal hlavu,
člověče! Teď klekneš, přijde-li, uhodíš čelem o zem a řekneš: Smilování,
tatarská kněžno; já už se víckrát neukážu.
Čajový pokojík slabě voní kdoulemi a září matně a měkce; zoufalá moucha naráží
hlavou o skla a úpí hlasem téměř lidským. Kam jsi dal hlavu, ty hlupče?
Princezna rychle, tiše vklouzla do pokoje. U dveří sáhla po vypínači a zhasla;
a potmě cítil Prokop ruku, jež se lehýnce dotýká jeho tváře a klade se mu
kolem krku. Sevřel princeznu v dlaních; je tak útlá a skoro netělesná, že se
jí dotýká s bázní jako něčeho křehoučkého a pavučinového. Dýchá mu do tváře
vzdušné polibky a šeptá něco nesrozumitelně; nehmotné hlazení mrazí Prokopovy
vlasy. Cosi zalomcuje křehoučkým tělem, ruka na jeho šíji se přimyká těsněji a
vlahé rty se pohybují na jeho ústech, jako by bezhlase a naléhavě mluvily.
Nekonečnou vlnou, celým přílivem záchvěvů zmocňuje se Prokopa silněji a
silněji; táhne k sobě jeho hlavu, tiskne se k němu hrudí a koleny, ovíjí ho
oběma pažema, vrhá se ústy do jeho úst; strašné, bolestné sevření drtící a
němé, náraz zubů o sebe, sténání člověka, který se dusí; vrávorají v
křečovitém, nepříčetném objetí, nepustit! zalknout se! srůst nebo zemřít!
Vzlykla a zalomila se bezvládně; uvolnil děsné kleště svých rukou, vymkla se,
zakolísala se jako opilá, vyňala ze záňadří šáteček a osušila na rtech sliny
nebo krev; a aniž řekla slova, vešla do sousedního osvětleného pokoje.
S hlavou praskající zůstal Prokop potmě. Toto poslední objetí mu připadlo jako
rozloučení.


XXXV.

Tlustý cousin měl pravdu: starého Hagena ranila z radosti mrtvice, ale
nedorazila ho ještě; ležel bez vlády obklopen doktory a namáhal se otevřít
levé oko. Narychlo byl přivolán oncle Rohn a jiné příbuzenstvo; starý kníže se
stále pokoušel zvednout levé víčko, aby pohleděl na svou dceru a řekl jí cosi
svým jediným živým okem.
Vyběhla prostovlasá, jak byla u jeho lůžka, a utíkala k Prokopovi, jenž od
rána hlídal v parku. Ani za mák nedbajíc Holze políbila ho rychle a zavěsila
se do něho; jen tak mimochodem se zmínila o otci a oncle Charlesovi, zaujatá
něčím, roztržitá a něžná. Tiskla mu paži a lísala se k němu, hned zase
nepřítomná a zamyšlená. Začal rýpat a žertovat o tatarské dynastii… tak trochu
nahroceně; šlehla po něm očima a zavedla řeč jinam, dejme tomu na včerejší
odpoledne. „Do poslední chvíle jsem myslela, že k tobě nepřijdu. Víš, že mně
je skoro třicet let? Když mně bylo patnáct, zamilovala jsem se do našeho
kaplana, ale strašně. Šla jsem se mu zpovídat, abych ho viděla zblízka; a
protože jsem se styděla říci, že jsem kradla nebo lhala, zpovídala jsem se mu,
že jsem zesmilnila; nevěděla jsem, co to je, měl chudák mnoho práce vymluvit
mně to. Teď už bych se mu nemohla zpovídat,“ dokončila tiše, a na rtech se jí
škubla nějaká hořkost.
Prokopa znepokojovala její stálá sebeanalýza, v níž tušil palčivou sebetrýzeň.
Hleděl nalézt jakékoliv jiné téma, ale shledával s hrůzou, že nemluví-li o
lásce, nemají vlastně o čem mluvit. Stáli na baště; princezně jaksi ulevovalo
vracet se zpátky, vzpomínat, povídat o sobě malé a důvěrné věci. „Brzo po té
zpovědi byl ti u nás učitel tance, a ten se miloval s mou guvernantkou,
takovou tlustou ženskou. Já jsem na to přišla a… viděla jsem je, víš? Mně se
to ošklivilo, oh! ale hlídala jsem je a… Já to nemohla pochopit. Ale pak
jednou při tanci jsem to zničehonic pochopila, když mne k sobě přitlačil. Pak
už na mne nesměl vůbec sáhnout; dokonce jsem… po něm… střelila z flobertky.
Museli je dát oba pryč.
V té době… v té době mě hrozně trápili matematikou. Mně vůbec nešla do hlavy,
víš? Učil mě takový zlý profesor, slavný učenec; vy učenci jste všichni divní.
Dal mi úkol a díval se na hodinky; za hodinu to muselo byt vypočítáno. A když
už mně zbývalo jenom pět minut, čtyři minuty, tři minuty, a já neměla ještě
nic, tu mi… zabouchalo srdce, a já měla… takový strašný pocit –“ Zaťala prsty
do Prokopovy paže a zasykla. „Pak už jsem se na ty hodiny i těšila.
V devatenácti mne zasnoubili; to ani nevíš, viď? A protože jsem už věděla
všechno, musel mně můj ženich přísahat, že se mne nikdy nedotkne. Za dvě léta
padl v Africe. Vyváděla jsem – z romantiky nebo proč – tak, že mne pak už
nikdy nenutili vdát se. Myslela jsem, že tím mám všecko odbyto.
A vidíš, tehdy jsem se do toho vlastně jen nutila, nutila jsem se věřit, že
jsem mu zůstala něco dlužna a že mu i po smrti musím stát v slově; až se mi
nakonec z toho všeho zdálo, že jsem ho milovala. Teď vidím, že jsem to všechno
jen hrála sama před sebou; a že jsem necítila nic víc, nic víc než hloupé
zklamání.
Viď, je to divné, že ti musím o sobě říkat takové věci? Víš, to je tak
příjemně nestydaté říci na sebe všechno; člověka přitom až mrazí, jako by něco
svlékal.
Když jsi sem přišel, napadlo mě na první pohled, že jsi jako ten profesor
matematiky. Já se tě až bála, ty milý. Teď mi zas dá takový úkol, lekla jsem
se, a už mi začalo bouchat srdce.
Koně, koně, to mne zrovna opíjelo. Když mám koně, myslela jsem, že nepotřebuju
lásku. A jezdila jsem jako divá.
Vždycky se mi zdálo, že láska, víš, je něco sprostého a… hrozně ošklivého.
Vidíš, teď se mi to už nezdá; a to mne právě tak děsí a pokořuje. A zas mne až
těší, že jsem jako každá jiná. Když jsem byla malá, bála jsem se vody. Učili
mne plavat na suchu, ale do rybníka jsem nešla; vymyslila jsem si, že tam jsou
pavouci. A jednou to na mne přišlo, taková odvaha nebo zoufalství: zavřela
jsem oči, udělala křížek a skočila. Neptej se, jak jsem byla potom pyšná; jako
bych ve zkoušce obstála, jako bych všechno poznala, jako bych se celá změnila.
Jako bych teprv teď vyspěla… Milý, milý, zapomněla jsem udělat křížek.“
Kvečeru přišla do laboratoře, neklidná a zaražená. Když ji vzal do náruče,
koktala s hrůzou: „Otevřel oko, otevřel oko, oh!“ Myslela tím starého Hagena;
odpoledne (neboť Prokop číhal jako blázen) měla dlouhý hovor s oncle Rohnem,
ale nechtěla o tom mluvit. Vůbec zdálo se, že dychtí něčemu uniknout; vrhala
se do Prokopova objetí tak žíznivě a oddaně, jako by se chtěla za každou cenu
omámit do bezvědomí. Konečně strnula s očima zavřenýma, chabá jako věchýtek;
myslel, že spí, ale tu počala šeptat: „Milý, nejmilejší, já něco provedu, já
provedu něco hrozného; ale pak, pak mne už nesmíš opustit. Přísahej, přísahej
mi,“ drtila divoce a vyskočila, ale hned to zas přemohla. „Ach ne. Co bys mi
mohl přísahat? V kartách mně vyšlo, že odejdeš. Chceš-li to udělat, udělej to,
udělej to ještě teď, dokud není pozdě.“
Prokop, to se rozumí, vyletěl jako raketa: že ona se ho chce zbavit, že jí
stoupla do hlavy tatarská pýcha a kdesi cosi. Rozčilila se a křičela na něho,
že je nízký a surový, že si to zapovídá, že… že…; ale jen to z ní vylítlo, už
mu s výkřikem visela na krku zdrcená a kající: „Jsem zvíře, viď? Já to tak
nemyslela. Vidíš, princezna nikdy nekřičí; zamračí se, odvrátí se, a dost,
stačí to; ale na tebe křičím jako… jako bych byla tvá žena. Bij mne, prosím
tě. Počkej, ukážu ti, že i já bych dovedla… Pustila ho a zničehonic, tak jak
byla, jala se poklízet laboratoř; dokonce namočila pod hydrantem hadr a
pustila se kolenačky do utírání celé podlahy. Mělo to být patrně pokání; ale
nějak se jí to zalíbilo, rozjařila se, oháněla se hadrem po podlaze a bzučela
si písničku, kterou pochytla někde od služek, „až půjdeš spat“ či něco
podobného. Chtěl ji zvednout. „Ne, počkej,“ bránila se, „ještě tamhle.“ A již
vlezla s hadrem pod stůl.
„Prosím tě, pojď sem,“ ozvalo se za chvíli zpod stolu udiveně. Brumlaje
jistými rozpaky vsoukal se za ní. Seděla v dřepu, objímajíc kolena rukama.
„Ne, jen se podívej, jak vypadá stůl zespoda. Já to ještě nikdy neviděla. Nač
to tak je?‘ Položila mu na tvář ruku prokřehlou od mokrého hadru. „Hu, studím,
viď? Ty jsi celý tak hrubě udělaný jako stůl zespoda; to je na tobě to
nejkrásnější. Jiní… jiné lidi jsem viděla jen tak, víš? po té hladké,
ohoblované straně; ale ty, ty jsi na první pohled samý takovýhle trám a
štěrbina a všecko to, víš, čím drží lidský tvor pohromadě. Když se po tobě
jede prstem, zadře si člověk třísku; ale přitom je to tak krásně a poctivě
uděláno – Člověk to začne vidět jinak a… vážněji než po té hladké straně. To
jsi ty.“
Schoulila se vedle něho jako starý kamarád. „Mysli si, člověče, že jsme třeba
ve stanu, nebo v takovém srubu,“ šeptala vyjeveně. „Já jsem si nikdy nesměla
hrát s kluky; ale někdy jsem… tajně… šla za zahradníkovými hochy, a lezla s
nimi po stromech a přes ploty… Pak se doma divili, proč mám roztrhané kalhoty.
A když jsem se tak ztratila a letěla za nimi, to mi tak krásně tlouklo srdce
strachem – Když jdu za tebou, mám ti docela ten krásný strach jako tehdy.“
„Teď jsem tak dobře schovaná,“ bzučela šťastně, ležíc mu hlavou na kolenou.
„Sem za mnou nemůže nic. I já jsem po rubu, jako ten stůl; obyčejná ženská,
která nemyslí na nic a jenom se kolébá – Proč je člověku tak dobře v úkrytu?
Tak vidíš, teď vím, co je štěstí; to se musejí zavřít oči… a udělat se malý…
docela maličký a k nenalezení…“
Kolébal ji mírně a hladil jí rozpoutanou hřívu; ale jeho oči se vytřeštily
přes její hlavu do prázdna.
Prudce k němu obrátila tvář. „Nač jsi teď myslel?“
Uhnul plaše očima. Nemohl jí přece říci, že viděl před sebou tatarskou
princeznu v její slávě, tvora veličenského a ztuhlého pýchou, a to že je ta,
kterou i teď… kterou v muce a touze…
„Nic, nic,“ zabručel nad pokorným a šťastným uzlíčkem na svých kolenou, a
pohladil snědé líčko. Zapálilo se milostnou vášnivostí.


XXXVI.

Lépe by byl učinil, kdyby byl toho večera nepřišel; ale přihnal se právě
proto, že mu to zakazovala. Oncle Charles byl k němu velmi, velmi přívětivý;
naneštěstí viděl, jak si ti dva při docela nevhodné a okaté příležitosti
tisknou ruce, vzal si na to dokonce monokl, aby to lépe viděl; pak teprve
princezna utrhla ruku a začervenala se jako školačka. Oncle k ní přistoupil a
něco jí pošeptal odváděje ji pryč. Pak se již nevrátila; jen Rohn přišel a
tvářil se jakoby nic a mluvil s Prokopem, velmi diskrétně sonduje po citlivých
místech. Prokop se držel neobyčejně hrdinně, nevyzradil nic, což milého
strýčka uspokojilo, i když ne co do věci, tedy aspoň co do formy. „Ve
společnosti je nutno být velice, velice obezřelý,“ řekl posléze, udíleje takto
zároveň důtku i radu; a Prokopovi se velmi ulevilo, když ho hned potom opustil
a nechal přemýšlet o dosahu posledních slov.
Horší ještě bylo, že se podle všech známek něco vařilo pod pokličkou; zejména
starší příbuzné zrovna pukaly důležitostí.
Když pak ráno Prokop obíhal kolem zámku, přišla k němu komorná a udýchaně
vyřizovala, že prý má pán jít do březového hájku. Pustil se tam a čekal
předlouho. Konečně běží princezna dlouhými, krásnými skoky Diany. „Schovej
se,“ šeptá rychle, „oncle jde za mnou.“ Uhánějí držíce se za ruce a zmizí v
hustém lupení černého bezu; pan Holz, marně se ohlížeje po jiné houštině, se
položí obětavě do kopřiv. A tu již je vidět světlý klobouk oncle Rohna; jde
bystře a kouká napravo nalevo. Princezně jiskří oči radostí jako mladé faunce;
v keři to voní vlhkostí a ztuchlinou, tajemný hmyzí život opřádá větvičky a
kořání, jsou jako v džungli; a nečekajíc ani, až nebezpečí přejde, táhne k
sobě princezna Prokopovu hlavu. Ochutnává mezi zuby polibky, jako by to byly
bobulky jeřabin či dřínu, ovoce trpké a milé; je to vábení, hra, uhýbání,
rozkoš tak nová a překvapující, že jim je, jako by se viděli poprvé.
A toho dne k němu nepřišla; bez sebe všemožným podezřením pustil se k zámku;
čekala na něho, vodíc se s Egonkem kolem krku. Sotva ho uviděla, nechala Egona
stát a šla k němu, bledá, zaražená, přemáhající nějaké zoufalství. „Oncle už
ví, že jsem byla u tebe,“ řekla. „Bože, co se stane! Myslím, že tě odtud
odvezou. Nehýbej se teď, dívá se na nás z okna. Mluvil odpoledne s tím… s
tím…“ Zachvěla se. „S ředitelem, víš? Hádali se… Oncle chtěl, aby tě prostě
pustili, aby tě nechali utéci či co. Ředitel zuřil, nechce o tom slyšet. Prý
tě odvezou někam jinam… Milý, buď tady v noci; přijdu ven, uteču, uteču –“
Přišla skutečně; přiběhla bez dechu, vzlykajíc se suchýma a rozzelýma očima.
„Zítra, zítra,“ chtěla ze sebe něco vypravit, ale tu se jí položila na rameno
silná a laskavá ruka. Byl to oncle Rohn. „Jdi domů, Minko,“ kázal neodmluvně.
„A vy tu počkáte,“ obrátil se k Prokopovi, a ovinuv jí ruku kolem ramen vedl
ji mocí domů. Po chvíli vyšel a vzal Prokopa pod paží. „Můj milý,“ řekl bez
hněvu, překusuje jakýsi smutek, „chápu až příliš dobře vás mladé lidi; a…
cítím s vámi.“ Mávl nad tím beznadějně rukou. „Stalo se, co se stát nemělo.
Nechci ovšem a… ani nemohu vás kárat. Naopak uznávám, že… samozřejmě…“
Samozřejmě to byl špatný začátek, a le bon prince tápal po jiném. „Milý
příteli, vážím si vás a… mám vás opravdu… velice rád. Jste člověk čestný… a
geniální, což se zřídka najde spojeno. K málokomu jsem pojal takové sympatie –
Vím, že to přivedete nesmírně daleko,“ vyhrkl s úlevou. „Věříte, že to s vámi
dobře myslím?“
„Naprosto ne,“ mínil Prokop mírně, střeha se sednout na nějakou vějičku.
Le bon oncle se zmátl. „To je mi líto, neobyčejně líto,“ koktal. „K tomu, co
jsem vám chtěl říci, by bylo třeba – ano – plné vzájemné důvěry –“
„Mon prince,“ přerušil ho Prokop uctivě, „jak víte, nejsem tu v záviděníhodné
situaci volného člověka. Myslím, že za těch okolností nemám důvodů tak příliš
důvěřovat –“
„Aáno,“ oddychl si oncle Rohn potěšen tímto obratem. „Máte zcela pravdu.
Narážíte na své – eh, na trapný fakt, že jste tady střežen? Vidíte, právě o
tom jsem chtěl s vámi mluvit. Milý příteli, co mne se týče… Prostě od začátku…
a rozhořčeně… jsem odsuzoval tento způsob… vás držet v závodě. Je to
nezákonné, brutální a… vzhledem k vašemu významu přímo neslýchané. Podnikl
jsem řadu kroků… Rozumíte, už dříve,“ dodával rychle. „Intervenoval jsem
dokonce na vyšších místech, ale… úřady jsou vzhledem k jistému mezinárodnímu
napětí… v panice. Jste tu… konfinován pod inkulpací špionáže. Nedá se nic
dělat, leda,“ a mon prince se naklonil k Prokopovu uchu, „leda že by se vám
podařilo utéci. Svěřte se mi, já vám opatřím prostředky. Čestné slovo.“
„Jaké prostředky?“ nadhodil Prokop nezávazně.
„Prostě… udělám to sám. Vezmu vás na své auto a – mne tady nemohou zadržet,
rozumíte? Ostatní později. Kdy chcete?“
„Odpusťte, já vůbec nechci,“ odpověděl Prokop určitě.
„Proč?“ vyhrkl oncle Charles.
„Předně… nechci, abyste vy, mon prince, něco takového riskoval. Osobnost jako
vy –“
„A za druhé?“
„Za druhé se mi tu začíná líbit.“
„A dál, dál?“
„Nic dál,“ usmál se Prokop, a snesl pátravý, vážný pohled princův.
„Poslyšte,“ ozval se oncle Rohn po chvíli, „nechtěl jsem vám to říci. Jde o
to, že za den, za dva dny máte být převezen jinam, do pevnosti. Stále pod
obviněním ze špionáže. Nemůžete si představit – Milý příteli, uprchněte,
uprchněte rychle, pokud je čas!“
„Je to pravda?“
„Čestné slovo.“
„Pak… pak vám děkuju, že jste mne včas upozornil.“
„Co uděláte?“
„Nu, připravím se na to,“ prohlásil Prokop krvelačně. „Mon prince, mohl byste
JE upozornit, že to… tak lehko… nepůjde.“
„Co – co – jak to, prosím?“ koktal oncle Charles.
Prokop zatočil rukou ve vzduchu, až to svištělo, a vrhl něco imaginárně před
sebe. „Bum,“ udělal.
Oncle Rohn ustrnul. „Vy se chcete bránit?“
Prokop neřekl nic; stál s rukama v kapsách, mračil se hrozně a uvažoval.
Oncle Charles, celý světloučký a vetchý v noční tmě, k němu přistoupil. „Vy…
vy ji tak milujete?“ řekl skoro se zajíkaje dojetím nebo obdivem.
Prokop neodpověděl. „Milujete ji,“ opakoval Rohn a objal ho. „Buďte silný.
Opusťte ji, odjeďte! Nemůže to takhle zůstat, pochopte to, pochopte to přece!
Kam by to vedlo? Prosím vás proboha, mějte s ní slitování; uchraňte ji
skandálu; což si myslíte, že by mohla být vaší ženou? Snad vás miluje, ale –
je příliš pyšná; kdyby se měla zříci titulu princezny… Oh, to je nemožno,
nemožno! Nechci vědět, co mezi vámi bylo; ale odjeďte, máte-li ji rád! odjeďte
rychle, odjeďte ještě této noci! Ve jménu lásky odjeď, příteli; zapřísahám tě,
prosím tě na jejím místě; učinils ji ženou nejnešťastnější, – nemáš dost na
tom? Chraň ji, nedovede-li už ona sebe chránit! Ty ji miluješ? Tedy se
obětuj!“
Prokop stál nehnutě, s čelem skloněným jako beran; ale bon prince cítil, jak
se tenhle černý, hrubý špalek v nitru štípe a praská bolestí. Soucit mu škrtil
srdce, ale ještě měl v záloze jednu zbraň; nedalo mu to, musel s ní vyrukovat.
„Je pyšná, fantastická, šíleně ctižádostivá; od dětství byla taková. Nyní nám
byly doručeny doklady nesmírné ceny; je kněžnou rovnorodou kterékoliv
panovnické rodině. Ty nechápeš, co to pro ni je. Pro ni i pro nás. Snad to
jsou předsudky, ale… my v nich žijeme. Prokope, princezna se provdá. Vezme si
velkovévodu bez trůnu; je to hodný a pasívní člověk, ale ona, ona bude bojovat
o korunu; neboť bojovat, to je její povaha, její poslání, její pýcha – Teď se
před ní otvírá, o čem snila. Ještě ty se stavíš mezi ni a… její budoucnost;
ale již se rozhodla, už jenom se trápí výčitkami –“
„Ahaha,“ rozkřikl se Prokop, „tohle tedy? A – a ty si myslíš, že teď, teď
ustoupím? Tak si jen počkej!“
A nežli se oncle Rohn vzpamatoval, zmizel mu ve tmě uháněje k laboratoři. Pan
Holz mlčky za ním.


XXXVII.

Když doběhl k laboratoři, chtěl zavřít Holzovi dveře před nosem, aby se uvnitř
opevnil; ale panu Holzovi se povedlo ještě včas zašeptat: „Princezna.“
„Co je?“ obrátil se k němu Prokop chvatně.
„Ráčila mně nařídit, abych byl s vámi.“
Prokop nebyl s to potlačit radostné překvapení. „Podplatila tě?“
Pan Holz zavrtěl hlavou a jeho pergamenová tvář se poprvé zasmála. „Podala mně
ruku,“ řekl uctivě. „Slíbil jsem jí, že se vám nic nestane.“
„Dobře. Máš bouchačku? Tedy budeš hlídat dveře. Nikdo ke mně nesmí, rozumíš?“
Pan Holz kývl; a Prokop provedl důkladnou strategickou prohlídku celé
laboratoře co do její nedobytnosti. Poněkud uspokojen nastavil na stůl různé
plechovky, dóze a kovové krabičky, co kde mohl sehnat, a objevil s nemalou
radostí celou spoustu hřebíků; nato se pustil do práce.
Ráno se pan Carson jakoby nic coural k Prokopově laboratoři; už zdálky ho
uviděl, jak se před barákem bez kabátu patrně pocvičuje ve vrhání kamenů. „Moc
zdravý sport,“ křikl zdálky vesele.
Prokop si honem oblékal kabát. „Zdravý a užitečný,“ odpovídal ochotně. „Tak co
mi jdete říci?“
Kapsy jeho kabátu ohromně odstávaly a harašilo to v nich. „Co to máte v
kapsách?“ ptal se pan Carson ledabyle.
„Takový chlorazid,“ povídal Prokop. „Třaskavý a dusivý chlor.“
„Hm. Proč to nosíte po kapsách?“
„Jen tak, pro špás. Chcete mi něco říci?“
„Teď už nic. Zatím raději nic,“ řekl pan Carson znepokojen a drže se poměrně
daleko. „A co ještě máte v těch – v těch škatulkách?“
„Hřebíky. A tohle,“ ukazoval vytahuje z kapsy u kalhot krabičku od vazelíny,
„je benzoltetraoxozonid, novinka dernier cri. He?“
„Nemusel byste tím tak mávat,“ mínil pan Carson ustupuje ještě dál. „,Máte
snad nějaké přání?“
„Mé přání?“ řekl Prokop přívětivě. „Rád bych, abyste JIM něco vyřídil. Že
odtud především nepůjdu.“
„Dobře, rozumí se. A dál?“
„A že kdyby někdo na mne neopatrně sáhnul… nebo mně chtěl jít zbytečně na
tělo… Doufám, že mne nemíníte nechat zavraždit.“
„Naprosto ne. Čestné slovo.“
„Můžete jít blíž.“
„Nevyletíte do povětří?“
„Dám pozor. Chtěl jsem vám ještě říci, aby nikdo nevlezl tady do mé pevnosti,
když tam nebudu. Na dveřích je třaskavá šňůra. Pozor, člověče; za vámi je
past.“
„Výbušná?“
„Jenom s diazobenzolperchlorátem. Musíte dát lidem výstrahu. Tady nemá nikdo
co hledat, že? Dále, mám jisté důvody… cítit se ohrožen. Rád bych, abyste
nařídil tuhle Holzovi, že mne má osobně chránit… před každým zásahem. Se
zbraní v ruce.“
„To ne,“ bzučel Carson. „Holz bude přeložen.“
„Kdepak,“ protestoval Prokop, „já se bojím být sám, víte? Poručte mu to
laskavě.“ Přitom se blížil mnohoslibně ke Carsonovi, chrastě přitom, jako by
byl celý z plechu a hřebíků.
„Nu budiž,“ řekl honem Carson. „Holzi, budete střežit pana inženýra. Kdyby mu
někdo chtěl ublížit – K sakru, dělejte si už, co chcete. Přál byste si ještě
něco?“
„Zatím nic. Kdybych něco chtěl, přijdu za vámi.“
„Děkuju uctivě,“ zahučel pan Carson a honem se zachránil z nebezpečné oblasti.
Ale jen doběhl do své kanceláře a telefonoval na všechny strany ty nejnutnější
rozkazy, když to zachřestilo na chodbě a do dveří vrazil Prokop, naditý pumami
z plechovek, že na něm praskaly švy.
„Poslyšte,“ spustil Prokop bledý vztekem, „kdopak dal rozkaz nevpustit mne do
parku? Buď ten rozkaz hned odvoláte, nebo –“
„Zůstaňte kousek dál, ano?“ vyhrkl Carson drže se za psacím stolem. „Co mně je
u čerta po vaší – – po vašem parku? Jděte si –“
„Počkat,“ zarazil ho Prokop a nutil se vysvětlit mu to trpělivě: „Dejme tomu,
jsou okolnosti, kdy… kdy je někomu docela jedno, co se stane,“ zařval náhle,
„rozumíte?“ Haraše a chrastě vrhl se k nástěnnému kalendáři. „Úterý, dnes je
úterý! A tuhle, tuhle mám –“ Hrabal se horečně v kapsách, až vylovil
porcelánovou schránku na mýdlo dosti chatrně ovázanou provázkem. „Zatím pět
deka. Víte, co to je?“
„Krakatit? Vy nám jej nesete?“ vydechl pan Carson ozářen náhlou nadějí. „Pak –
pak ovšem –“
„Nic pak,“ šklebil se Prokop a stopil schránku do kapsy. „Ale dopálíte-li mne,
pak… pak to mohu rozsypat, kde budu chtít, že? Tak co?“
„Tak co?“ opakoval Carson mechanicky, úplně zdrcen.
„Nu, zařiďte to, aby zmizel ten pacholek u vchodu. Já se rozhodně chci projít
v parku.“
Pan Carson přezkoumal rychle Prokopa, a pak si naplil pod nohy. „Fi,“
prohlásil přesvědčeně, „to jsem to provedl pitomě!“
„Provedl,“ souhlasil Prokop. „Ale mne taky dřív nenapadlo, že mám tuhle barvu
v listě. Tak co?“
Carson potrhl rameny. „Zatím… Božínku, to je maličkost! Já jsem hrozně rád, že
vám to mohu udělat. Na mou čest, ohromně rád. A co vy? Dáte nám těch pět
deka?“
„Nedám. Zruším je sám; ale… dřív chci vidět, že platí naše stará smlouva.
Volný pohyb a tak dále, co? Pamatujete se?“
„Stará smlouva,“ bručel pan Carson. „Čert vem starou smlouvu. Tehdy jste ještě
nebyl – tehdy jste ještě neměl poměr –“
Prokop k němu skočil, až to zařinčelo. „Co jste to řekl? Cože jsem neměl?“
„Nic, nic,“ pospíšil si pan Carson rychle mrkaje. „Já nic nevím. Mně nic není
do vašich soukromých věcí. Chcete-li se procházet po parku, je to vaše věc, no
ne? Jen spánembohem už jděte a –“
„Poslyšte,“ řekl Prokop podezíravě, „ne aby vás napadlo přerušit elektrické
vedení do mé laboratoře. Sic bych –“
„Dobře, dobře,“ ujišťoval pan Carson. „Status quo, že? Mnoho štěstí. – Uf,
zatracený člověk,“ doložil zdrceně, když už byl Prokop za dveřmi.
Řinče železem pustil se Prokop do parku, těžký a masívní jako houfnice. Před
zámkem stála skupina pánů; sotva ho zdálky zahlédli, dali se poněkud zmateně
na ústup, patrně už informováni o brizantním a nabitém zuřivci; a jejich záda
vyjadřovala nejsilnější pohoršení, že se „něco takového trpí“. Tamhle jde pan
Krafft s Egonem, konaje peripatetické vyučování; jak vidí Prokopa, nechá Egona
stát a běží k němu. „Můžete mi podat ruku?“ ptá se a zardívá se nad vlastním
hrdinstvím. „Teď dostanu jistě výpověď,“ praví s hrdostí. Od Kraffta tedy
zvěděl, že v zámku se rychlostí blesku rozneslo, že prý on, Prokop, je
anarchista; a ježto zrovna dnes večer má sem zavítat jistý následník trůnu…
Zkrátka chtějí Jeho Výsosti telegrafovat, aby svůj příjezd odložil; zrovna se
o tom koná velká rodinná rada.
Prokop se obrací na patě a jde do zámku. Dva komorníci na chodbě se před ním
rozletí a s hrůzou se tisknou ke zdi, nechávajíce beze slova projít
chřestícího, naditého útočníka. Ve velkém salóně zasedá porada; oncle Rohn
ustaraně přechází, starší příbuzné se děsně rozčilují nad zvrhlostí
anarchistů, tlustý cousin mlčí a jakýsi jiný pán rozhorleně navrhuje poslat na
toho šíleného chlapa jednoduše vojáky: buď se vzdá, nebo bude zastřelen. V tu
chvíli se otevřely dveře a chrastě valí se Prokop do salónu. Hledá očima
princeznu; není tu, a zatímco všichni tuhnou strachem a vstávají v očekávání
toho nejhoršího, povídá chraptivě k Rohnovi: „Jdu vám jenom říci, že se
následníkovi nic nestane. Teď to víš.“ Pokynul hlavou a mocně se vzdálil jako
socha komtura.


XXXVIII.

Chodba byla prázdná. Kradl se, jak tiše to vůbec šlo, k pokojům princezniným a
čekal přede dveřmi, nepohnutý jako plechový rytíř tam dole ve vestibulu.
Vyběhla komorná, vykřikla strašně, jako by viděla bubáka, a zmizela ve
dveřích. Po chvíli je otevřela, docela vytřeštěná, a couvajíc mu ukázala beze
slova dovnitř, načež se co nejrychleji ztratila. Princezna se mu vlekla
vstříc; halila se v dlouhý plášť, patrně jen tak vyskočila z postele, a vlasy
nad čelem měla slepené a zmáčené, jako by právě odhodila chladivý obklad, a
byla šedivě bledá a nehezká. Pověsila se mu na krk a zvedla k němu rty
rozpukané horkostí. „To jsi hodný,“ šeptala mátožně. „Mně hlava třeští
migrénou, oh bože! Prý máš samé pumy po kapsách? Já se tě nebojím. Jdi teď,
nejsem hezká. Přijdu k tobě v poledne, nepůjdu jíst, řeknu, že mi není dobře.
Jdi.“ Dotkla se jeho úst obolenými, loupajícími se rty a zakryla si tváře, aby
ji ani neviděl.
Provázen panem Holzem vracel se do laboratoře; každý před ním stanul, uhnul,
uskočil raději až za příkop. Pustil se znovu do práce jako posedlý; mísil
látky, jež by nikoho nenapadlo mísit, slepě a bezpečně jist, že tohle je
třaskavina; plnil tím lahvičky, škatulky od sirek, plechové konzervy, všecko,
co mu padlo do rukou; měl toho plný stůl, okenní rámy i podlahu, překračoval
to, neměl se už kde postavit. Po poledni vklouzla k němu princezna zastřená
závojem a zahalená v plášti až po nos. Běžel k ní a chtěl ji obejmout,
odstrčila ho. „Ne, ne, nejsem dnes hezká. Prosím tě, pracuj; budu se na tebe
dívat.“
Usedla na krajíček židle zrovna uprostřed strašného arzenálu oxozonidových
třaskavin. Prokop rychle, se sevřenými rty něco vážil a mísil, zasyčelo to,
kysele začpělo, načež to s neskonalou pozorností filtroval. Dívala se na jeho
ruce nehnutýma, palčivýma očima. Oba mysleli na to, že dnes přijede následník.
Prokop hledal něco očima na regálu s lučebninami. Vstala, pozvedla závoj,
vzala ho kolem krku a přimkla se pevně k jeho ústům sevřenými suchými rty.
Potáceli se mezi lahvemi s vratkým oxozobenzolem a děsnými fulmináty, dvojice
němá a křečovitá; ale opět ho odstrčila a usedla zastírajíc si obličej. Ještě
rychleji, sledován jejíma očima, dal se Prokop do práce jako pekař mísící
chléb; a toto bude látka z nejďábelštějších, jaké kdy člověk vyrobil;
nedůtklivá hmota, vzteklý a hrůzně citlivý olej, prchlost a náruživost sama. A
toto, průhledné jako voda, těkavé jako éter, to tedy je ono: děsná věc trhavá
a nevypočítatelná, divost nejvýbušnější. Ohlížel se, kam postavit láhev
naplněnou tímto nepojmenovaným. Usmála se, vzala mu ji z ruky a chovala ji na
klíně mezi sepnutýma rukama.
Venku pan Holz křikl na někoho: „Stůj!“ Prokop vyběhl ven. Byl to oncle Rohn
stojící povážlivě blízko třaskavé pasti.
Prokop šel až k němu. „Co tu hledáte?“
„Minku,“ řekl oncle Charles krotce, „není jí dobře, a proto –“
Prokopovi to škublo ústy. „Pojďte si pro ni,“ řekl a dovedl – ho dovnitř.
„Ach, oncle Charles,“ vítala ho princezna přívětivě. „Pojď se dívat, je to
hrozně zajímavé.“
Oncle Rohn se podíval pátravě po ní a po světnici, a ulevilo se mu. „To bys
neměla, Minko,“ pronesl káravě.
„Proč ne?“ namítla nevinně.
Bezradně pohlédl na Prokopa. „Protože… protože máš horečku.“
„Tady mi je líp,“ děla klidně.
„Vůbec bys neměla…,“ vzdychl le bon prince vážně se kaboně.
„Mon oncle, víš, že vždycky dělám, co chci,“ ukončila neodvolatelně rodinný
výstup, zatímco Prokop odklízel ze židle krabičky s fudroajantní
diazosloučeninou. „Posaďte se,“ zval Rohna zdvořile.
Oncle Charles nezdál se nadšen celou situací. „Nezdržujeme vás… nezdržujeme tě
v práci?“ ptal se Prokopa bezcílně.
„Naprosto ne,“ řekl Prokop drtě mezi prsty infuzorní hlinku.
„Co to děláš?“
„Třaskaviny. Prosím, tu láhev,“ obrátil se k princezně.
Podala mu ji a „Tumáš,“ řekla provokativně a naplno. Oncle Rohn sebou trhl,
jako by ho píchl; ale tu již ho upoutala rychlá sice, ale nekonečně opatrná
pečlivost, s níž Prokop odkapával čirou tekutinu na hromádku hlinky.
Odkašlal a ptal se: „Čím se to může zanítit?“
„Otřesem,“ odpověděl Prokop dále odpočítávaje kapky.
Oncle Charles se otočil po princezně. „Bojíš-li se, oncle,“ řekla suše,
„nemusíš na mne čekat.“
Usadil se rezignovaně a zaklepal holí na plechovou krabici od kalifornských
broskví. „Co je v tomhle?“
„To je ruční granát,“ vysvětloval Prokop. „Hexanitrofenylmetylnitramin a
šroubové matičky. Potěžkej to.“
Oncle Rohn upadl v rozpaky. „Nebylo by snad… na místě… trochu víc opatrnosti?“
ptal se toče mezi prsty krabičkou od sirek, kterou sebral na pultě.
„Zajisté,“ souhlasil Prokop a vzal mu krabičku z rukou. „To je chlorargonát. S
tím si nehraj.“
Oncle Charles se zamračil. „Mám z toho všeho… trochu nepříjemný dojem
zastrašování,“ podotkl ostře.
Prokop hodil krabičku na stůl: „Tak? A já zas měl dojem zastrašování, když
jste mi hrozili pevností.“
„… Mohu říci,“ pravil Rohn spolknuv tu námitku, „že na mne to celé počínání…
zůstává bez vlivu.“
„Ale na mne má ohromný vliv,“ prohlásila princezna.
„Bojíš se, že něco provede?“ obrátil se k ní le bon prince.
„Já doufám, že něco provede,“ řekla nadějně. „Myslíš, že by to nedovedl?“
„O tom nepochybuji,“ vyhrkl Rohn. „Půjdeme už?“
„Ne. Já bych mu chtěla pomáhat.“
Zatím Prokop přelamoval v prstech kovovou lžičku. „K čemu je to?“ ptala se
zvědavě.
„Došly mně hřebíky,“ bručel. „Nemám čím plnit bomby.“ Rozhlížel se hledaje
něco kovového. Tu princezna vstala, zarděla se, strhla si chvatně rukavičku a
smekla s prstu zlatý prsten. „Vezmi si to,“ děla tiše, zalita ruměncem a s
očima sklopenýma. Přijal jej trna; bylo to téměř slavnostní… jako zasnoubení.
Váhal potěžkávaje prsten v dlani; zvedla k němu oči v naléhavé a horoucí
otázce; i pokývl vážně a položil prsten na dno plechové krabičky.
Oncle Rohn starostlivě, přesmutně mrkal ptačíma očima poety.
„Teď můžeme jít,“ zašeptala princezna.
Kvečeru přijel dotyčný následník bývalého trůnu. U vchodu čestná rota,
hlášení, špalír služebnictva a takové ty okolky; park i zámek slavnostně
osvětleny. Prokop seděl na návršíčku před laboratoří a díval se mračnýma očima
k zámku. Nikdo tudy nešel; bylo ticho a temno, jen zámek zářil prudkými snopy
paprsků.
Prokop vzdychl ode dna a vstal. „Do zámku?“ ptal se pan Holz a přendal
revolver z kapsy u kalhot do kapsy svého věčného gumáku.
Jdou parkem už zhaslým; dvakrát nebo třikrát ustoupí před nimi nějaká postava
do houští, asi padesát kroků za nimi je pořád slyšet něčí chůzi ve spadaném
listí, ale jinak je tu pusto, syrově pusto. Jen v zámku plane celé křídlo
velkými zlatými okny.
Je podzim, je už podzim. Zda ještě v Týnici stříbrně odkapává studna? Ani vítr
nevane, a přece to zebavě šustí, na zemi nebo ve stromech? Na nebi rudou
proužkou padá hvězda.
Několik pánů ve fracích, hle, jak jsou skvělí a šťastní, vycházejí na plošinu
zámeckých schodů, žvaní, pokuřují, smějí se a už se zas vracejí. Prokop
nehnutě sedí na lavičce, otáčeje v roztřískaných prstech plechovou krabičkou.
Někdy si zachrastí jako dítě svým chřestítkem. Je tam rozlámaná lžička, prsten
a bezejmenná látka.
Pan Holz se ostýchavě přiblížil. „Dnes nemůže přijít,“ povídá šetrně.
„Já vím.“
V hostinském křídle se rozžíhají okna. Tato řada, to jsou „knížecí pokoje“.
Nyní svítí celý zámek, vzdušný a prolamovaný jako sen. Všechno tam je:
bohatství neslýchané, krása, ctižádost a sláva a hodnosti, plíšky na prsou,
požitky, umění žít, jemnost a duchaplnost a sebevědomí; jako by to byli jiní
lidé – jiní lidé než my –
Jako umíněné dítě řinčí Prokop svým chřestítkem. Ponenáhlu okna zhasínají;
ještě svítí tamto, jež je Rohnovo, a toto červené, kde je ložnice princeznina.
Oncle Rohn otvírá okenice a vdechuje noční chlad; a potom přechází ode dveří k
oknu, ode dveří k oknu, pořád a pořád. Za zastřeným oknem princezniným se
nehýbe ani stín.
I oncle Rohn už zhasil; nyní svítí jediné zardělé okno. Zda najde lidská
myšlenka cestu, zda si prorazí a mocí provrtá dráhu těmi sto či kolika metry
němého prostoru, aby zasáhla bdící mozek druhého člověka? Co ti mám vzkázat,
tatarská kněžno? Spi, je už podzim; a je-li nějaký Bůh, ať ti hladí palčivé
čelo.
Červené okno zhaslo.


XXXIX.

Ráno se rozhodl nejít do parku; měl právem za to, že by tam překážel. Umístil
se v poměrně úzké a polopusté končině, kde byla přímá cesta od zámku k
laboratořím, proražená skrze starý zarostlý val. Vydrápal se na val, odkud,
jakžtakž skryt, mohl vidět roh zámku a malou část parku. Místo se mu zalíbilo;
zahrabal si tam několik svých ručních granátů a pozoroval střídavě park,
chvátajícího střevlíka a vrabce na rozhoupaných větvičkách. Jednou tam slétla
dokonce červenka, a Prokop bez dechu pozoroval její brunátné hrdélko; tíkla
něco, mžikla ocasem a frr, pryč.
Dole v parku jde princezna provázena dlouhým, mladým člověkem; v uctivé
vzdálenosti za nimi skupina pánů. Princezna se dívá stranou a hází rukou, jako
by v ní měla prut a švihala jím do písku. Víc není vidět.
O hodně později se ukáže oncle Rohn s tlustým cousinem. Pak zase nic. Stojí-li
pak za to tady sedět?
Je skoro poledne. Najednou za rohem zámku se vynoří princezna a míří rovnou
sem. „Jsi tady?“ volá polohlasně. „Pojď dolů a vlevo.“
Svezl se po svahu a prodíral se houštinou vlevo. Bylo tam při zdi smetiště
všeho možného: rezavých obručí, děravých plecháčů, rozbitých cylindrů, všivých
a ohavných trosek; bůhví kde se takových věcí vůbec nabere v knížecím zámku. A
před tou bídnou hromadou stojí princezna svěží a pěkná a kouše se dětsky do
prstu. „Sem jsem se chodila zlobit, když jsem byla malá,“ povídá. „Nikdo to
místo nezná. Líbí se ti tu?“
Viděl, že by ji mrzelo, kdyby to tu nepochválil. „Líbí,“ řekl honem.
Zazářila a vzala ho kolem krku. „Ty milý! Dávala jsem si tu na hlavu nějaký
plecháč, víš, jako korunu, a hrála jsem si sama pro sebe na panující kněžnu.
,Jasnost nejmilostivější kněžna ráčí poroučet?‘ ,Zapřáhni šestispřeží, pojedu
do Zahur.‘ Víš, Zahur, to bylo mé vymyšlené místo. Zahur, Zahur! Milý, je něco
takového na světě? Pojď, ujedeme do Zahur! Najdi mi to, ty tolik znáš –“
Nikdy nebyla tak svěží a oživená jako dnes; až na to zažárlil, až v něm
zakvasilo vášnivé podezření; popadl ji a chtěl ji sevřít. „Ne,“ bránila se,
„nech; buď rozumný. Ty jsi Prospero, princ zahurský; a jen jsi se převlékl za
kouzelníka, abys mne unesl nebo vyzkoušel, já nevím. Ale za mnou přijede princ
Rhizopod z říše Alicuri-Filicuri-Tintili-Rhododendron, takový protivný,
protivný člověk, co má místo nosu kostelní svíci a studené ruce, hu! A už mne
má dostat za ženu, když ty vstoupíš a řekneš: ,Já jsem kouzelník Prospero,
dědičný princ zahurský.‘ A mon oncle Metastasio ti padne kolem krku, a začnou
zvonit, troubit a střílet –“
Prokop příliš dobře pochopil, že její líbezné tlachání povídá něco velmi,
velmi vážného; střežil se ji vyrušit. Držela ho kolem krku a třela se vonným
líčkem i rty o jeho drsnou tvář. „Nebo počkej; já jsem princezna zahurská a ty
jsi Velký Prokopokopak, král duchů. Ale já jsem zakletá, řekli nade mnou ,ore
ore baléne, magot malista manigoléne‘, a proto mne má dostat ryba, ryba s
rybíma očima a rybíma rukama a celým rybím tělem, a má mne odvést na rybí
hrad. Ale tu přiletí Velký Prokopokopak na svém větrném plášti a unese mne –
Sbohem,“ skončila znenadání a políbila ho na ústa. Ještě se usmála, jasná a
růžová jako nikdy, a nechala ho zamračeného nad rzivými troskami Zahuru. U
všech všudy, co tohle znamená? Žádá, abych jí pomohl, toť jasno; podléhá
nátlaku a čeká ode mne, že… že snad ji nějak zachráním! Bože, co učinit?
Hluboce zamyšlen se loudal Prokop k laboratoři. Patrně… už nezbývá než Veliký
Útok; ale kde jej zahájit? Už byl u dveří a sahal do kapsy po klíči; vtom
ustrnul a strašlivě zaklel. Zevní vrata jeho baráku byla zatarasena příčnými
železnými tyčemi na způsob závor. Zalomcoval jimi zběsile; vůbec se to
nehnulo.
Na dveřích byl list papíru a na něm vyklepáno: „Na rozkaz civilních úřadů se
tento objekt uzavírá pro nepřípustné nahromadění třaskavých látek bez
zákonných bezpečnostních opatření, §§ 216 a 217d, lit. F tr. z. a nař. 63 507,
M 1889.“ Podpis nečitelný. Pod tím napsáno perem: „Panu ing. Prokopovi se až
na další přikazuje k pobytu pokoj u hlídače Gerstensena, strážní barák III.“
Pan Holz odborně zkoumal závory, ale nakonec jen hvízdl a strčil ruce do
kapes; nedalo se naprosto nic dělat. Prokop, rozpálený vztekem do běla, oběhl
celý barák; explozívní pasti byly zákopnicky odstraněny, na všech oknech od
dřívějška mříže. Honem spočítal své válečné prostředky: pět slabších pumiček
po kapsách, čtyři větší granáty zahrabány na zahurském valu; je to málo na
slušnou akci. Bez sebe hněvem uháněl ke kanceláři zlořečeného Carsona; počkej,
všiváku, s tebou si to vyřídím! Ale jen tam doběhl, hlásil mu sluha: pan
ředitel tu není a nepřijde. Prokop ho odstrčil a vnikl do kanceláře. Carson
tam nebyl. Prošel rychle všemi kancelářemi, uváděje v úděs veškero úřednictvo
závodu až po poslední slečinku u telefonu. Carson nikde.
Prokop tryskem běžel k zahurskému valu, aby zachránil aspoň svou munici. A
tumáš: celý val i s křovinatou džunglí a zahurským smetištěm je dokola obtočen
kozami s ostnatým drátem: hotový zásek válečného řádu. Pokusil se rozmotat
dráty; ruce mu krvácely, ale nepořídil zhola nic. Vzlykaje vztekem a nedbaje
už ničeho, propletl se zásekem dovnitř; našel, že jeho čtyři velké granáty
jsou vyhrabány a pryč. Skoro plakal bezmocí. Ke všemu počalo slizce mžít.
Prodral se zpět, potrhán na cáry a krváceje z rukou i tváří, a hnal se do
zámku, snad aby tam našel princeznu, Rohna, následníka nebo koho. Ve vestibulu
se mu postavil do cesty onen plavý obr, odhodlán nechat se třeba i na kusy
roztrhat. Prokop vyňal jednu ze svých třaskavých plechovek a výstražně
zachrastil. Obr zamrkal, ale neustoupil; najednou se vrhl vpřed a sevřel
Prokopa kolem ramen. Holz ho vší silou praštil revolverem do prstů; obr zařval
a pustil, tři lidé, kteří se hrnuli na Prokopa, jako by ze země vyvstali,
zaváhali maličko, a tu se ti dva honem přitočili zády ke zdi, Prokop s
krabičkou ve zdvižené ruce, aby ji hodil pod nohy prvnímu, kdo se hne, a Holz
(nyní už neodvolatelně zrevolucionovaný) s nastraženým ústím revolveru; a
proti nim čtyři bledí muži, trochu nachýlení vpřed, tři s revolvery v ruce; to
bude mela. Prokop nalíčil strategickou diverzi ke schodům; čtyři muži se
začali přetáčet v tu stranu, někdo vzadu se dal na útěk, bylo hrozně ticho.
„Nestřílet,“ zašeptal kdosi ostře. Prokop slyší tikat své hodinky. Nahoře v
patře hlaholí veselé hlasy, nikdo tam o ničem neví; a protože nyní je východ
volný, točí se Prokop pozpátku ke dveřím, kryt Holzem. Čtyři muži u schodů se
nehýbají, jako by byli vyřezáni ze dřeva. A Prokop vyrazil ven.
Mží chladně a protivně; co nyní? Rychle přezkoumal situaci; napadlo ho zařídit
si vodní pevnost v plovárně na rybníce; ale odtamtud není vidět na zámek.
Náhle rozhodnut pádil Prokop k domku vrátného; Holz za ním. Vrazili dovnitř,
když děda vrátný zrovna obědval; naprosto nemohl pochopit, že ho „násilím a
pod vyhrůžkami smrtí“ odtud vyhánějí; vrtěl nad tím hlavou a šel to žalovat na
zámek. Prokop byl svrchovaně spokojen dobytou pozicí; důkladně zamknul mřížová
vrata z parku ven a dojedl s notnou chutí staříkův oběd; pak snesl všechno, co
se v domečku podobalo chemikálii, jako uhlí, sůl, cukr, klih, zaschlou
olejovou barvu a jiné takové poklady, a uvažoval, co se z toho dá udělat.
Zatím Holz chvílemi hlídal, chvílemi přeměňoval okna ve střílny, což vzhledem
k jeho čtyřem ostrým šestimilimetrovým patronám bylo trochu přepjaté. Prokop
na kuchyňských kamínkách zařídil svou laboratoř; páchlo to hrozně, a přece z
toho nakonec byla trochu těžkopádná třaskavina.
Nepřátelská strana nepodnikla žádný útok; patrně nechtěla, aby došlo k
skandálu za přítomnosti vznešeného hosta. Prokop si lámal hlavu, jak by mohl
zámek vyhladovět; přeřízl sice telefonní vedení, ale zbývala ještě trojí
vrátka, nepočítajíc cestu zahurským valem k závodům. Vzdal se tedy – byť nerad
– plánu oblehnout zámek ze všech stran.
Pršelo ustavičně. Princeznino okno se otevřelo, a světlá postavička psala
rukou do vzduchu veliká písmena. Prokop nebyl s to je rozluštit, přesto však
se postavil před domek a psal rovněž do vzduchu povzbuzující vzkazy, máchaje
rukama jako větrník. Kvečeru přeběhl k povstalcům dr. Krafft; ve svém
ušlechtilém zápalu zapomněl s sebou přinést jakoukoliv zbraň, takže tato
posila byla spíše jen mravní. Večer se přišoural pan Paul a nesl v koši
nádhernou studenou večeři a množství rudého a šampaňského vína; tvrdil, že ho
nikdo s tím neposlal. Nicméně Prokop po něm naléhavě – neříkaje komu –
vzkázal, „že děkuje a že se nevzdá“. Při bohatýrské večeři se dr. Krafft
poprvé odhodlal pít víno, snad aby dokázal svou mužnost; následek toho byla na
jeho místě blažená lunatická němota, zatímco Prokop a pan Holz se pustili do
zpěvu válečných písní. Každý sice zpíval jiným jazykem a docela jinou
písničku, ale zdálky, zejména potmě za šelestění drobného deště, to splývalo v
souzvuk dosti strašlivý a chmurný. Někdo v zámku dokonce otevřel okno, aby
poslouchal; pak se pokusil je zdálky doprovázet na klavíru, ale zvrhlo se to v
Eroiku a potom v nesmyslné bouchání do kláves. Když zámek pohasl, zatarasil
Holz dvéře nesmírnou barikádou, a tři bohatýři pokojně usnuli. Probudil je
teprve důtklivým boucháním pan Paul, když jim ráno nesl tři kávy pečlivě je
rozlévaje po táce.


XL.

Pršelo. S bílým šátkem parlamentáře přišel tlustý cousin navrhnout Prokopovi,
aby toho nechal; že zase dostane svou laboratoř a kdesi cosi. Prokop
prohlásil, že odtud nepůjde, ledaže by ho vyhodili do povětří; ale dřív že on
něco udělá, to budete koukat! S touto temnou hrozbou se cousin vracel; v zámku
patrně nesli velmi těžce, že vlastní vjezd do zámku je blokován, ale nechtěli
s věcí dělat žádný hluk.
Dr. Krafft, pacifista, přetékal bojovnými a divokými návrhy: přerušit
elektrické vedení do zámku; zastavit jim vodovod; vyrobit nějaký dusivý plyn a
pustit jej na zámek. Holz našel staré noviny; ze svých tajemných kapes vylovil
skřipec a četl po celý den, nesmírně podoben univerzitnímu docentovi. Prokop
se nezkrotně nudil; hořel touhou po nějakém velikém činu, ale nevěděl, jak do
toho. Konečně nechal Holze hlídat domek a pustil se s Krafftem do parku.
V parku nebylo vidět nikoho; nepřátelské síly byly asi soustředěny v zámku.
Obešel zámek až na tu stranu, kde byly kůlny a stáje. „Kde je Whirlwind?“ ptal
se najednou. Krafft mu ukázal okénko ve výši asi tří metrů. „Opřete se,“
šeptal Prokop, vylezl mu na záda a postavil se mu na ramena, aby se podíval
dovnitř. Krafft div nepadl pod jeho tíhou; a teď mu ještě ke všemu jaksi
tancuje po ramenou – co to tam dělá? Nějaký těžký rám letí na zem, ze stěny se
drolí písek; a náhle se břemeno vyhouplo, užaslý Krafft zvedl hlavu a div
nevykřikl: nahoře se třepají dvě dlouhé nohy a mizí v okénku.
Princezna zrovna podávala Whirlwindovi krajíc chleba a dívala se zamyšleně na
jeho krásné temné oko, když slyšela šramot v okně; a v pološeru teplé konírny
vidí známou potlučenou ruku, jak vyndává drátěnou mřížku v okénku stáje.
Přitiskla ruce k ústům, aby nevykřikla.
Rukama a hlavou napřed sváží se Prokop do Whirlwindovy žebřiny; již seskakuje
a tady je, odřený sice, ale celý; a udýchán se pokouší o úsměv. „Tiše,“ děsí
se princezna, neboť štolba je za dveřmi; a už mu visí na krku: „Prokopokopak!“
Ukázal na okénko: tudy, a rychle ven! „Kam?“ šeptá princezna a mazlivě ho
celuje.
„K vrátnému.“
„Ty hloupý! Kolik vás tam je?“
„Tři.“
„Tak vidíš, to přece nejde!“ Hladí ho po tváři. „Nic si z toho nedělej.“
Prokop rychle uvažuje, jak tedy jinak ji unést; ale je tu šero, a koňský
zápach je jaksi vzrušující; zasvítily jim oči a vpili se do sebe žádostivým
polibkem. Zlomila se ve vteřině; ucouvla rychle dýchajíc: „Jdi pryč! Jdi!“
Stáli proti sobě třesouce se; cítili, že vášeň, která je popadá, je nečistá.
Odvrátil se a ukroutil příčku v žebřině; teprve tím se jakžtakž ovládl. Otočil
se k ní; viděl, že rozkousala a roztrhala na cáry svůj kapesník; přitiskla jej
prudce ke rtům a beze slova mu jej podala odměnou nebo na památku. Za to on
políbil pažení na místě, kde právě spočívala její rozčilená ruka. Nikdy se
neměli tak divoce rádi jako v tuto chvíli, kdy nemohli ani promluvit a báli se
sebe dotknout. Na dvoře skřípaly v písku něčí kroky; princezna mu kynula,
Prokop se vyšvihl na žebřinu, chytil se jakýchsi háků u stropu a nohama napřed
se vysmekl z okénka. Když dopadl na zem, objal ho dr. Krafft radostí. „Vy jste
přeřezal koním šlachy, že?“ šeptal krvežíznivě; nejspíš to považoval za
oprávněné válečné opatření.
Prokop mlčky uháněl k vrátnici, bodán starostí o Holze. Už zdálky viděl
hroznou skutečnost: dva chlapi stáli ve fortně, zahradník zahrabával v
rozrytém písku stopy zápasu, mřížová vrata byla pootevřena a Holz pryč; a
jeden z chlapů měl ruku ovázanou šátkem, protože ho Holz patrně pokousal.
Prokop se stáhl do parku zachmuřený a němý. Dr. Krafft si myslel, že jeho
velitel kuje nový válečný plán, a nerušil ho; a Prokop s těžkým vzdechem usedl
na pařez a ponořil se do pozorování jakýchsi rozškubaných krajkových hadříků.
Na cestičce se vynořil dělník strkající trakař se smeteným listím. Krafft,
popaden podezřením, se do něho pustil a namlátil mu strašně; při tom ztratil
skřipec a nemohl jej bez skřipce nalézt; vzal tedy trakař jakožto kořist
zůstavenou na bojišti a spěchal s ním k náčelníkovi. „Utekl,“ hlásil, a
krátkozraké oči mu vítězně plály. Prokop jen zabručel a probíral se dál v tom
měkkém běloučkém, co mu vlálo v prstech. Krafft se zaměstnával trakařem,
nevěda, k čemu je tahle trofej dobrá; konečně ho napadlo obrátit jej dnem
vzhůru, a zazářil: „Dá se na tom sedět!“
Prokop se zvedl a zamířil k rybníku; dr. Krafft za ním i s trakařem, snad pro
transport příštích raněných. Obsadili plovárnu vestavěnou na kůlech ve vodě.
Prokop obešel kabiny; ta největší byla princeznina, zůstalo tam ještě zrcadlo
a hřeben s několika vytrhanými vlasy, pár vlásniček a huňatý koupací plášť a
sandálky, věcičky důvěrné a opuštěné; zamezil sem Krafftovi přístup a obsadil
s ním pánskou kabinu na druhé straně. Krafft zářil: nyní měli dokonce loďstvo
skládající se ze dvou maňásků, kanoe a bachratého člunu, který představoval
jaksi jejich naddreadnought. Prokop dlouho mlčky přecházel po palubě plovárny
nad šedivým rybníkem; potom se ztratil v kabině princeznině, usedl na její
lehátko, vzal do náručí její huňatý plášť a zaryl se do něho tváří. Dr.
Krafft, který přes svou neuvěřitelnou pozorovací neschopnost měl nějaké tušení
o jeho tajemství, šetřil jeho citů; točil se po špičkách po koupelně, vyléval
hrncem vodu z baňaté bitevní lodi a sháněl příslušná vesla. Objevil v sobě
velký bojový talent; odvážil se na břeh a nanesl do plovárny kamení všeho
kalibru, až po desetikilové balvany vytržené z hráze; pak se jal odbourávati
prkno po prknu můstek, jenž vedl z pevniny do koupaliště; posléze byli spojeni
se souší jen dvěma holými trámy. Z vytrhaných prken získal materiál pro
zabednění vchodu a krom toho drahocenné rezavé hřebíky, jež natloukl do lopat
vesel hroty ven. Tím vznikla zbraň strašná a vskutku vražedná. Pořídiv to vše
a shledavaje, že dobré jest, byl by se rád tím pochlubil náčelníkovi; ten však
byl zamčen v kabině princeznině a snad ani nedýchal, jak tam bylo ticho. Tu
stanul dr. Krafft nad šedivou plochou rybníka, jenž chladně a tichounce
šplounal; někdy to zažbluňklo, jak se vymrštila ryba, někdy zašelestilo
rákosí; a dr. Krafftovi začalo být úzko z té samoty.
Pokašlával před vůdcovou kabinou a chvílemi něco polohlasně povídal, aby
upoutal jeho pozornost. Konečně Prokop vyšel se rty sevřenými a divnýma očima.
Krafft ho provedl po nové pevnosti, ukazoval mu všecko, předváděl dokonce, jak
daleko dohodí kamenem po nepříteli; přitom by byl málem sletěl do vody. Prokop
neřekl nic, ale vzal ho kolem krku a políbil na tvář; a dr. Krafft, celý rudý
radostí, by udělal s chutí desetkrát tolik co dosud.
Sedli si na lavičce u vody, kde se slunívala hnědá princezna. Na západě se
zvedly mraky a ukázala se nesmírně daleká, churavě nazlátlá nebesa; celý
rybník se rozžehl, roztřpytil, rozněžnil bledým a tklivým jasem. Dr. Krafft
rozvíjel zbrusu novou teorii o věčné válce, o nadpráví síly, o spáse světa
skrze hrdinství; bylo to v ukrutném rozporu s mučivou melancholií tohoto
podzimního soumraku, ale naštěstí dr. Krafft byl krátkozraký a mimoto
idealista, a následkem toho naprosto nezávislý na nahodilém okolí. Nehledíc ke
kosmické kráse této. chvíle cítili oba zimu a hlad.
A tamhle na souši krátkými, spěchavými krůčky jde pan Paul s košem na lokti,
rozhlíží se vpravo vlevo a chvílemi volá stařeckým hláskem:“Kuku! Kuku!“
Prokop jel k němu na bitevní lodi; mermomocí chtěl vědět, kdo ho s tím posílá.
„Prosím, nikdo,“ tvrdil stařík; „ale má dcera, jako Alžběta, je klíčnice.“ Byl
by se málem rozmluvil o své dceři Alžbětě; ale Prokop ho pohladil po bílých
vláscích a vzkázal někomu nejmenovanému, že je zdráv a při síle.
Dnes pil dr. Krafft skoro sám, žvanil, filozofoval a opět kašlal na všechnu
filozofii: čin prý, čin je všecko. Prokop se chvěl na princeznině lavičce a
díval se pořád na jednu hvězdu, bůhví proč si vybral zrovna tu, byla to
oranžová Betelgeuse ve hlavách Oriona. Nebyla to pravda, že je zdráv; píchalo
ho divně v těch místech, kde to v něm harašilo a šelestilo kdysi v Týnici,
motala se mu hlava a třásl se láman zimnicí. Když pak chtěl něco říci, mátl se
mu jazyk a jektal tak, že dr. Krafft vystřízlivěl a tuze se znepokojil. Honem
uložil Prokopa na lehátko v kabině a přikryl ho vším možným, i řasnatým
pláštěm princezniným, a vyměňoval mu na čele namočený ubrousek. Prokop tvrdil,
že je to rýma; k půlnoci usnul a blábolil, pronásledován děsnými sny.


XLI.

Ráno se Krafft probudil teprve Paulovým kukáním; chtěl vyskočit, ale byl úplně
zdřevěnělý, neboť celou noc mrzl a spal stočen jako pes. Když se konečně
jakžtakž sebral, shledal, že je Prokop pryč; a jedna lodička z jejich flotily
se kolébala u břehu. Měl velikou úzkost o svého vůdce, byl by ho šel hledat,
ale bál se opustit pevnost tak dobře vybudovanou. I zlepšoval na ní, co ještě
mohl, a vyhlížel krátkozrakýma očima Prokopa.
Zatím Prokop, který se probudil jako rozlámaný a s blátivou chutí v ústech,
zimomřivý a trochu omámený, byl už dávno v parku vysoko v koruně starého
dubiska, odkud bylo vidět celou frontu zámku. Točila se mu hlava, držel se
pevně větve, nesměl se podívat rovně dolů, nebo by se svalil závratí.
Tato strana parku už zřejmě platila za bezpečnou; i staré příbuzné se
odvažovaly aspoň na zámecké schody, páni se procházeli po dvou nebo po třech,
kavalkáda kavalírů se proháněla po hlavní cestě; u vrat zas se točí děda
vrátný. Po desáté hodině vyšla sama princezna provázena následníkem trůnu a
zamířila někam k japonskému pavilónu. V Prokopovi hrklo, zdálo se mu, že letí
hlavou dolů; křečovitě se chytil větve a třásl se jako list. Nikdo nešel za
nimi; naopak všichni honem vyklidili park a zdržovali se na prostranství před
zámkem. Asi rozhodující rozmluva nebo co. Prokop se kousal do rtů, aby
nevykřikl. Trvalo to nesmírně dlouho, snad hodinu nebo pět hodin. A teď běží
odtamtud následník sám, je rudý a zatíná pěstě. Panstvo před zámkem se
rozprsklo a počalo ustupovat, jako by mu dělalo místo. Následník nehledě
napravo ani nalevo běží po schodech; tam mu jde vstříc prostovlasý oncle Rohn,
chvilku spolu hovoří, le bon prince si přejede dlaní čelo a oba zajdou.
Panstvo před zámkem se přeskupuje, strká k sobě hlavy a kouskovitě se vytrácí.
Před zámek předjíždí pět automobilů.
Prokop chytaje se větví svezl se z koruny dubiska, až se zaryl do země; chtěl
tryskem běžet k japonskému altánu, ale bylo mu až směšné, jak nevládl nohama;
motal se, jako by šel mlhovým těstem, a nemohl jaksi nalézti ten altán, neboť
věci se mu před očima mátly a prostupovaly. Konečně to našel: tady sedí
princezna, šeptá něco před sebe přísnými rty a švihá do vzduchu proutkem.
Sebral všechny své síly, aby k ní junácky došel. Vstala a pokročila mu vstříc:
„Čekala jsem na tebe.“ Šel k ní a málem by do ní vrazil, neboť viděl ji pořád
jaksi daleko. Položil jí ruku na rameno, divně a násilně napřímen a trochu se
komihaje, a hýbal rtoma; myslel si, že mluví. Také ona něco povídá, ale není
jí rozumět; všechno se odehrává jako pod vodou. Tu zazněly sirény a houkačky
vyjíždějících aut.
Princezna sebou trhla, jako by jí kolena poklesla. Prokop vidí smazaný bledý
obličej, v němž plavou dva temné otvory. „To je konec,“ slyší jasně a zblízka,
„je konec. Milý, milý, já ho poslala pryč!“ Kdyby byl mocen smyslů, viděl by,
že je jako vyřezána ze sloni, ztuhlá a mučednicky krásná ve svrchovanosti své
oběti; ale jen mžikal přemáhaje mdlobný třas víček, a zdálo se mu, že se
podlaha pod ním zvedá, aby se překlopila. Princezna si přitiskla ruce k čelu a
zakolísala; právě se mu chtěla složit do loktů, aby ji nesl, aby ji podepřel
vyčerpanou skutkem příliš velikým; ale předešel ji a složil se bez hlesu u
jejích nohou; zhroutil se beztvaře, jako by byl jen z hader a provazů.
Neztratil vědomí; bloudil očima, naprosto nechápaje, kde vlastně je a co se s
ním děje. Zdálo se mu, že ho někdo zvedá jíkaje úděsem; chtěl sám napomoci,
ale nešlo to. „To je jen… entropie,“ řekl; zdálo se mu, že tím vystihuje
situaci, a opakoval to několikrát. Pak se mu něco rozlilo v hlavě s hukotem
jako jez; jeho hlava těžce vyklouzla z třesoucích se prstů princezniných a
bouchla o zem. Princezna vyskočila jako šílená a běžela pro pomoc.
Věděl nejasně o všem, co se děje; cítil, že ho zvedají tři lidé a vlekou ho
pomalu, jako by byl z olova; slyšel jejich těžké šoupavé kroky a rychlý dech,
a divil se, že ho nemohou unést jen tak v prstech jako onučku. Někdo ho po tu
celou dobu držel za ruku; obrátil se a poznal princeznu. „To jste hodný,
Paul,“ řekl jí vděčně. Pak nastala nějaká zmatená, udýchaná strkanice; to ho
nesli po schodech, ale Prokopovi se zdálo, že s ním padají kroužíce do
propasti. „Netlačte se tak,“ bručel, a hlava se mu zatočila tak, že přestal
vnímat.
Když otevřel oči, viděl, že leží zase v kavalírském pokoji a že ho Paul svléká
rozčilenýma rukama. U hlav stojí princezna s takhle velkýma očima, jako kola.
Prokop si všechno spletl. „Já jsem spadl s koně, že?“ breptal namáhavě. „Vy –
vy jste – vy jste byla při tom, že? Bum, vy-výbuch. Litrogly – nitrogry –
mikro – Cé há dvě o en o dvě. Kom-pli-kovaná fraktura. Kovaná, jako kůň.“
Umlkl, když pocítil na čele studenou úzkou ruku. Pak zahlédl toho řezníka
doktora a zaťal nehty do něčích chladných prstů. „Já nechci,“ úpěl, neboť se
bál, že to začne bolet; ale řezník jen položil hlavu na jeho prsa a dusil,
dusil jako cent. V úzkostech našel nad sebou temné a rozzelé oči, jež ho
fascinovaly. Řezník se zvedl a povídal někomu vzadu: „Chřipková pneumonie.
Odveďte Její Jasnost, to je nakažlivé.“ Někdo mluví pod vodou, a doktor
odpovídá: „Dojde-li k zpěnění plic, pak – pak –“ Prokop pochopil, že je
ztracen a že umře; ale bylo mu to svrchovaně lhostejno: tak jednoduché si to
nikdy nepředstavoval. „Čtyřicet celých sedm,“ povídá doktor. Prokop má jediné
přání: aby ho nechali vyspat, dokud neumře; ale místo toho ho balili do něčeho
studeného, ohoh! Konečně si tam šeptají; a Prokop zavřel oči a nevěděl dál o
ničem.
Když se probudil, stáli nad ním dva staří černí páni. Bylo mu neobyčejně
lehko. „Dobrý den,“ řekl a chtěl se pozvednout. „Nesmíte se hýbat,“ povídá
jeden pán a mírně ho tlačí do podušek. Prokop tedy poslušně leží. „Ale je mi
už lépe, že?“ ptá se spokojen. „To se rozumí,“ bručí druhý pán pochybovačně,
„ale nesmíte se vrtět. Klid, rozumíte?“
„Kde je Holz?“ napadlo Prokopa najednou.
„Zde,“ ozvalo se z kouta, a u nohou postele stojí pan Holz s hrozným
škrábancem a modřinou na tváři, ale jinak suchý a kožnatý jako vždy. A za ním,
propána, to je Krafft, Krafft zapomenutý v plovárně; má oči zpuchlé a rudé,
jako by tři dny brečel. Co se mu stalo? Prokop se na něho usmál, aby ho
potěšil. Také pan Paul jde po špičkách k posteli a drží si ubrousek na ústech.
Prokop má radost, že tu jsou všichni; bloudí očima po pokoji, a za zády obou
černých pánů objeví princeznu. Je na smrt bledá a hledí na Prokopa ostrýma,
zachmuřenýma očima, jež ho nepochopitelně děsí. „Mně už nic není,“ šeptá, jako
by se omlouval. Optala se očima jednoho z pánů, který rezignovaně pokývl.
Přistoupila tedy k posteli. „Je ti lépe?“ ptá se tiše. „Milý, milý, je ti
skutečně lépe?“
„Ano,“ řekl nejistě, trochu tísněn zaraženým chováním všech. „Skoro docela
dobře, jen – jen –“ Její upřené oči ho plnily zmatkem a téměř úzkostí; bylo mu
nevolno a svěravě.
„Přál by sis něco?“ ptala se naklánějíc se nad ním.
Pocítil divou hrůzu z jejího pohledu. „Spát,“ zašeptal, aby mu unikl.
Pohlédla tázavě na oba pány. Jeden maličko pokývl a podíval se na ni tak – tak
divně vážně. Pochopila a zesinala ještě víc. „Spi tedy,“ vypravila ze sebe
seškrceně a obrátila se ke zdi. Prokop se s podivením rozhlédl. Pan Paul měl
ubrousek nacpaný v ústech, Holz stál jako voják mrkaje očima a Krafft
jednoduše brečel opřen čelem o skříň a hlučně posmrkával jako uřvané dítě.
„Ale copak –,“ vyhrkl Prokop a chtěl se zvednout; ale jeden pán mu položil na
čelo ruku, jež byla tak měkká a dobrá, tak ujišťující a zrovna svatá na hmat,
že se rázem uklidnil a blaženě vzdychl. Usnul téměř okamžitě.
Probudil se teninkou nitkou polovědomí. Svítí jen lampička na nočním stolku, a
vedle postele sedí princezna v černých šatech a dívá se na něj lesklýma,
uhrančivýma očima. Rychle zavřel víčka, aby je už neviděl; tak úzko mu z nich
bylo.
„Drahý, jak je ti?“
„Kolik je hodin?“ zeptal se mátožně.
„Dvě.“
„Poledne?“
„V noci“
„Už,“ podivil se sám nevěda proč a snoval dál matnou nit spánku. Chvílemi
pootevřel štěrbinkou oči a vyhlédl po princezně, aby zas usnul. Proč se tak
pořád dívá? Někdy mu svlažila rty lžičkou vína; spolkl to a zamumlal něco.
Posléze zapadl do tupého a nevědomého spánku.
Procitl teprve tím, že jeden z černých pánů opatrně poslouchal na jeho prsou.
Pět jiných stálo kolem.
„Neuvěřitelno,“ bručel černý pán. „To je zrovna kovové srdce.“
„Musím zemřít?“ zeptal se Prokop znenadání. Černý pán málem vyskočil
překvapením.
„Uvidíme,“ řekl. „Když jste přečkal tuhle noc – Jak dlouho jste s tím chodil?“
„S čím?“ divil se Prokop.
Černý pán mávl rukou. „Klid,“ řekl, „jenom klid.“ Prokop, třeba mu bylo
nekonečně bídně, se ušklíbl; když si doktoři nevědí rady, vždycky předpisují
klid. Ale ten s dobrýma rukama mu povídal: „Musíte věřit, že se uzdravíte.
Víra dělá zázraky.“


XLII.

Vytřeštil se ze spánku zalit a promočen hrozným potem. Kde – kde to je? Strop
nad ním se kymácí a hourá; nenene, padá, šroubuje se dolů, sváží se pomalu
jako ohromný hydraulický lis. Prokop chtěl zařvat, ale nemohl; a strop už je
tak nízko, že na něm rozeznává sedící průsvitnou mušku, zrnéčko písku v
omítce, každou nepravidelnost nátěru; a pořád to klesá níž, a Prokop se na to
dívá s bezdechou hrůzou a jen sípe, nemoha ze sebe dostati hlasu. Světlo
zhaslo, je černá tma; teď ho to rozmačká. Prokop už cítí, jak se strop dotýká
jeho zježených vlasů, a bezhlase piští. Ahaha, teď nahmatal dveře, vyrazil je
a vrhl se ven; i tam je taková tma, ale není to tma, je to mlha, vlčí mlha,
mlha tak hustá, že nemohl dýchat a dusil se škytaje děsem. Teď mne to udusí,
zhrozil se a dal se na útěk, šlapaje popopo po nějakých ži-živých tělech, jež
se ještě kroutí. Sklonil se tedy a sáhl, a cítil pod rukou mladé široké ňadro.
To – to – to je Anči, lekl se, a hmatal jí po hlavě; ale místo hlavy to mělo
mísu, por-ce-lánovou mísu s něčím slizkým a houbovitým jako hovězí plíce. Bylo
mu k zvracení děsno a chtěl od toho odtrhnout ruce; ale ono to k nim lne,
třese se to, přisává se to a plazí se mu to po pažích nahoru. A ona je to
krakatice, mokrá a rosolovitá sépie s lesklýma očima princezny, jež se na něho
upírají náruživě a zamilovaně; sune se mu po holém těle a hledá, kam usadí
svou ohavnou, prýštící řiť. Prokop nemůže vydechnout, rve se s ní, zarývá
prsty do povolné klihovité hmoty; a procitl.
Byl nad ním nakloněn pan Paul a dával mu na prsa studený obkladek.
„Kde – kde – kde je Anči,“ zamumlal Prokop s úlevou a zavřel oči. Buch buch
buch běží uřícen přes oranice; neví, kam má tak naspěch, ale pádí, až mu srdce
taktaktak třeští, a chtěl by vyrazit jek úzkosti, že dorazí pozdě. A tady je
ten dům, jenže nemá dveří ani oken, jen nahoře hodiny a na nich za pět minut
čtyři. A Prokop rázem ví, že až bude rafije na dvanáctce, vyletí celá Praha do
povětří. „Kdo mně vzal Krakatit,“ ryčí Prokop; pokouší se vylézt po stěně, aby
zastavil ručičku hodin v poslední minutě; vyskakuje a zarývá nehty do omítky,
ale klouže dolů nechávaje ve zdi dlouhé škrábance. Vyje hrůzou a letí někam
pro pomoc. Vrazil do konírny; tam stojí princezna s Carsonem a milují se
trhanými, mechanickými pohyby jako panáčci na kamnech pohánění teplým
vzduchem. Když ho spatřili, vzali se za ruce a poskakovali rychle, rychle,
pořád rychleji.
Prokop vzhlédl a viděl nad sebou schýlenou princeznu se sevřenými rty a
palčivýma očima. „Zvíře,“ zamručel s chmurnou nenávistí a zavřel rychle oči.
Srdce mu tlouklo tak šíleně rychle, jako ti dva křepčili. V očích ho štípal
pot a v ústech cítil jeho slanost; jazyk měl připečený k patru a hrdlo slepené
suchou žízní. „Chceš něco?“ ptá se princezna zblizoučka. Zavrtěl hlavou.
Myslela si, že opět spí; ale ozval se po chvíli chraptivě: „Kde je ta obálka?“
Měla za to, že jen blábolí; neodpověděla. „Kde je ta obálka?“ opakoval vraště
panovačně čelo. „Tady je, tady,“ řekla honem a vsunula mu mezi prsty první
kousek papíru, který měla po ruce. Smačkal jej prudce a zahodil. „To není ona.
Já – já chci svou obálku. Já – já – já chci svou obálku.“
Opakoval to ustavičně, začal zuřit, i zavolala Paula. Paul se rozpomněl na
jakousi silnou, usmolenou a převázanou obálku; kde honem je? Našel ji v nočním
stolku: tady je, nu vida! Prokop ji sevřel v obou rukou a přitiskl k prsoum;
utišil se a usnul jako zabitý. Po třech hodinách se zarosil novým vydatným
potem; byl tak zesláblý, že sotva dýchal. Princezna zalarmovala lékařské
konzilium. Teplota povážlivě poklesla, tep sto sedm, puls nitkovitý; chtěli mu
dát ihned kafrovou injekci, ale místní venkovský doktor, zeselštělý a
ostýchavý mezi takovými kapacitami, mínil, že on nikdy pacienta nebudí. „Aspoň
zaspí svůj exitus, že?“ bručel slavný odborník. „Máte dobře.“
Princezna, úplně vysílená, si šla na hodinku lehnout, když ji ujistili, že
bezprostředně a tak dále; a u pacienta zůstal dr. Krafft, slíbiv, že za hodinu
jí vzkáže, jak a co je. Nevzkázal nic, a znepokojená princezna se šla podívat.
Našla Kraffta, jak stojí uprostřed pokoje, máchá rukou a z plných plic káže o
telepatii dovolávaje se Richeta, Jamese a kdekoho; a Prokop ho s jasnýma očima
poslouchá a sem tam ho popichuje námitkami vědeckého a omezeného nevěrce, „Já
ho vzkřísil, princezno,“ křičel Krafft zapomínaje na vše, „já jsem upnul svou
vůli na to, aby se uzdravil; já… já jsem nad ním dělal takhle rukama, víte?
Vyzařování ódu. Ale to člověka vyčerpá, uf! Jsem jako moucha,“ prohlásil a
vypil naráz plnou sklenici benzínu na koupání pravazek, pokládaje to nejspíš
za víno; tak byl rozčilen svým úspěchem. „Řekněte,“ křičel, „uzdravil jsem vás
nebo ne?“
„Uzdravil,“ řekl Prokop s přívětivou ironií.
Dr. Krafft se zhroutil do lenošky. „To jsem si sám nemyslel, že mám tak silnou
auru,“ oddychl si spokojeně. „Mám na vás ještě vzkládat ruce?“
Princezna pohlížela užasle z jednoho na druhého, zruměnila celá, zasmála se,
najednou se jí zamžily oči, pohladila Kraffta po zrzavé lbi a utekla.
„Ženská nic nevydrží,“ konstatoval Krafft pyšně. „Vidíte, já jsem docela
klidný. Cítil jsem, jak mně to fluidum vyvěrá z prstů. Jistě by se to dalo
fotografovat, víte? jako ultrazáření.“
Přišly kapacity, vyhodily především Kraffta přes jeho protesty a znovu měřily
teplotu, puls a všechno možné. Teplota vyšší, puls devadesát šest, pacient
jeví chuť k jídlu; nu, to už je slušný obrat. Načež se kapacity odebraly do
druhého křídla zámku, kde jich bylo také třeba; neboť princezna hořela skoro
čtyřiceti stupni horečky, nadobro sesutá po šedesáti hodinách bdění; mimoto
silná anémie a celá řada jiných nemocí až po zanedbané tuberkulózní ložisko.
Den nato už Prokop v posteli seděl a slavně přijímal návštěvy. Veškeré panstvo
se sice rozjelo, jen tlustý cousin tu ještě otálel nudě se a vzdychaje.
Přihnal se Carson trochu rozpačitý, ale dopadlo do dobře; Prokop se o ničem
minulém nezmínil, a konečně Carson vyhrkl, že ty hrozné třaskaviny, které
Prokop vyráběl v posledních dnech, se při zkoušce ukázaly asi tak výbušné jako
piliny; zkrátka – zkrátka musel mít Prokop už pořádnou horečku, když je dělal.
I to přijal pacient klidně, a dal se teprve po chvíli do smíchu. „No víte,“
řekl dobromyslně, „ale přesto jsem vám nahnal pořádně strachu.“
„Nahnal,“ přiznával se Carson ochotně. „Jakživ jsem se tolik nebál o sebe a o
fabriku.“
Krafft se přivlekl zsinalý a zkroušený. Oslavoval v noci své zázračné fluidum
velkými úlitbami vína, a nyní mu bylo prábídně. Bědoval, že navždycky utopil
svou ódickou sílu, a umiňoval si od nynějška indickou askezi podle jógy.
Přišel i oncle Charles, byl trčs aimable a jemně zdrženlivý; Prokop mu byl
vděčen, že le bon prince našel pěkný tón jako před měsícem, znovu mu vykaje a
zábavně povídaje o svých zkušenostech. Jen když se hovor vzdáleně dotkl
princezny, padala na ně jistá rozpačitost.
Zatím princezna v druhém křídle suše, bolestně pokašlávala a přijímala každé
půl hodiny Paula, který musel povídat, co dělá Prokop, co jedl, kdo je u něho.
Ještě se mu vracely horečky s děsnými sny. Viděl temnou kůlnu a nekonečné řady
sudů s Krakatitem; před kůlnou chodí vojáček se zbraní sem a tam, sem a tam;
nic víc, ale bylo to hrůzné. Zdálo se mu, že je zase ve válce; před ním
nesmírné pole s mrtvými, všichni jsou mrtvi, i on je mrtvý a přimrzlý ledem k
zemi; jen pan Carson klopýtá přes mrtvoly, sakruje mezi zuby a dívá se
netrpělivě na hodinky. Z druhé strany se škubavými, posunčivými pohyby blíží
chromý Hagen; jde kupodivu rychle, skáče jako polní kobylka a vrže při každém
křečovitém pohybu. Carson nedbale pozdraví a něco mu povídá; Prokop marně
napíná uši, neslyší ani slova, snad to odnáší vítr; Hagen ukazuje předlouhou
vychrtlou rukou k obzoru; co si to povídají? Hagen se odvrátí, sáhne si k
ústům a vyjme odtamtud žlutý koňský chrup i s čelistmi; místo úst má nyní
propadlou černou díru, jež se bezhlase chechtá. Druhou rukou si vydloubne z
očnice ohromnou bulvu oka a drže ji mezi prsty nastavuje ji zblízka k tváři
padlým; a žlutý chrup v jeho druhé ruce skřehotavě počítá: „Sedmnáct tisíc sto
dvacet jedna, sto dvacet dvě, sto dvacet tři.“ Prokop se nemůže odvrátit,
neboť je mrtev; děsná krvavá bulva utkví nad jeho lící, a koňský chrup
zaskřehotá „sedmnáct tisíc sto dvacet devět“ a cvakne. Nyní se již Hagen
ztrácí v dálce, pořád počítaje; a přes mrtvoly skáče princezna se sukněmi
nestoudně vyhrnutými vysoko nad kraj kalhot, blíží se k Prokopovi a mává v
ruce tatarským bunčukem, jako by to byl bičík. Stane nad Prokopem, zalechtá ho
bunčukem pod nosem a šťouchá ho špičkou nohy do hlavy, jako by zkoušela, je-li
mrtev. Tryskla mu krev do tváří, ač byl skutečně mrtev, tak mrtev, že cítil v
sobě srdce zmrzlé na kost; avšak nemohl snést pohled na její ztepilé nohy.
„Milý, milý,“ zašeptala a pomalým pohybem spustila sukně, klekla mu u hlav a
sahala dlaní lehýnce po jeho prsou. Najednou mu vytrhla z kapsy tu silnou
převázanou obálku a vyskočila, roztrhala ji zuřivě na kousky a hodila do
větru. Pak se s rukama rozpřaženýma roztočila a vířila, vířila šlapajíc po
mrtvých, až zmizela v noční tmě.


XLIII.

Neviděl princeznu od té doby, co ulehla; jen mu psala několikrát denně
kratičké a horoucí dopisy, jež víc tajily než povídaly. Od Paula slyšel, že
polehává a opět přechází po pokojích; nemohl pochopit, že k němu nepřijde, sám
již vstával z postele a čekal, že ho aspoň na minutku zavolá. Nevěděl, že ona
zatím plivá krev z tuberkulózní kaverny, která se v ní akutně otevřela;
nenapsala mu to, patrně se děsila, že by se mu jaksi ošklivěla, že by ho
pálily na rtech stopy jejích někdejších polibků nebo co; a hlavně, hlavně se
hrozila toho, že by se nezdržela a i nyní ho pocelovala horečnými rty. Neměl
tušení, že v jeho vlastních chrchlech našli doktoři stopy infekce, což uvádělo
princeznu v zoufalství sebeobviňování a úzkosti; nevěděl prostě nic, vztekal
se, že s ním dělají takové okolky, když už se cítí skoro zdráv, a trnul
studeným děsem, když opět uplynul den, aniž princezna projevila přání vidět
ho. Omrzel jsem se jí, napadlo ho; nikdy jsem pro ni nebyl víc než chvilkový
rozmar. Podezříval ji ze všeho možného; nechtěl se ponížit k tomu, aby sám
naléhal na schůzku, nepsal jí skorem a jen čekal v lenošce s rukama a nohama
ledovějícíma, že ona přijde, že vzkáže, že se něco vůbec stane.
Za slunečných dnů smí už ven do podzimního parku, smí posedět na výsluní
obalen plédy; chtěl by je shodit a potloukat se někde u rybníka se svými
černými myšlenkami, ale vždycky je tu Krafft, Paul nebo Holz nebo i sám Rohn,
vlídný a zamyšlený poeta Charles, který má pořád něco na jazyku, ale nikdy to
neřekne; místo toho rozjímá o vědě, osobní zdatnosti, úspěchu a hrdinství a já
nevím o čem ještě. Prokop poslouchá jedním uchem; má dojem, že le bon prince
se neobyčejně namáhá zainteresovat jej bůhvíproč na vysoké ctižádosti.
Zničehonic dostal od princezny zmatenou bumagu, aby se držel a nebyl
ostýchavý; a hned nato k němu Rohn přivedl úsečného starého pána, na kterém
vše prozrazovalo oficíra převlečeného do civilu. Úsečný pán se vyptával
Prokopa, co hodlá podnikat v budoucnosti. Prokop, trochu dopálen jeho tónem,
odpovídal bryskně a velkopansky, že hodlá vytěžit své vynálezy.
„Vojenské vynálezy?“
„Nejsem voják.“
„Váš věk?“
„Třicet osm.“
„Služba?“
„Žádná. A vaše?“
Úsečný pán se trochu zmátl. „Míníte své vynálezy prodat?“
„Ne.“ Cítil, že je vyslýchán a potěžkáván vysoce oficiálně; nudilo ho to,
odrýval stručné odpovědi a jen tu a tam ráčil utrousit špetku své učenosti
nebo hrst balistických čísel, vida, že tím dělá Rohnovi zvláštní radost.
Skutečně, le bon prince zářil a pořád pokukoval na úsečného pána, jako by se
ho ptal: Nu tak, co říkáte tomu zázraku? Úsečný pán však neřekl nic a konečně
se vlídně poroučel.
Den nato přiletěl Carson hned ráno, mnul si nadšeně ruce a vypadal nesmírně
důležitě. Tlachal páté přes deváté a pořád sondoval; nadhazoval neurčitá
slovíčka, jako „budoucnost“ a „kariéra“ a „báječný úspěch“; víc nechtěl říci,
zatímco Prokop se nechtěl vůbec ptát. A pak přišlo psaní od princezny, bylo
velmi vážné a divné: „Prokope, dnes bude na Tobě učinit rozhodnutí. Já je
učinila a nelituji toho. Prokope, v této poslední chvíli Ti pravím, že Tě
miluji a budu na Tebe čekat, jak dlouho bude třeba. I kdybychom se museli
načas odloučit – a musí to být, neboť Tvá žena nemůže být Tvou milenkou –,
kdyby nás na léta rozloučili, budu Ti pokornou nevěstou; už to, už to mi je
takovým štěstím, že Ti to nemohu říci; chodím po pokoji omámená a koktám Tvé
jméno; milý, milý, nedovedeš si představit, jak jsem byla nešťastna od té
chvíle, co se to s námi stalo. A nyní učiň, abych se mohla opravdu jmenovati
Tvou W.“
Prokop tomu dobře nerozuměl; četl to bezpočtukrát a nemohl prostě uvěřit, že
princezna míní zkrátka a dobře… Chtěl se k ní rozběhnout, ale nevěděl kudy kam
ukrutnými rozpaky. Snad je to jen nějaký ženský nebo citový výbuch, který se
nesmí brát doslova a kterému vůbec nerozumím; což se v ní vyznáš? Zatímco
takto rozjímal, přišel k němu oncle Charles provázený Carsonem. Oba vypadali
tak… oficiálně a slavnostně, že v Prokopovi hrklo: Jdou mně říci, že teď mne
odvezou na pevnost; princezna něco zavařila, a je zle. Hledal očima nějakou
zbraň, kdyby snad došlo k násilí; vybral si mramorové těžítko a usedl
přemáhaje tlučení srdce.
Oncle Rohn se podíval na Carsona a Carson na Rohna s němou otázkou, kdo má
začít. Začal tedy oncle Rohn: „To, co vám jdeme říci, je… do jisté míry…
nepochybně…“ Bylo to známé Rohnovo plavání; ale náhle se sebral a dal se do
toho odvážněji: „Můj milý, co ti jdeme říci, je věc velmi vážná a… diskrétní.
Není to jenom v tvém zájmu, abys učinil… nýbrž naopak… Zkrátka byla to nejprve
její myšlenka a… co mne se týče, tu po zralé úvaze… Ostatně jí nelze klást
mezí; je umíněná… a vášnivá. Mimoto vskutku, jak se zdá, si vzala do hlavy…
Zkrátka je na všechny strany lépe najít slušné východisko,“ vyhrkl s úlevou.
„Pan ředitel ti to vysvětlí.“
Carson, čili pan ředitel, si nasadil pomalu a důstojně brejle; vypadal až
znepokojivě vážně, docela jinak než kdy dosud. „Je mi ctí,“ začal, „tlumočit
vám přání… našich nejvyšších vojenských kruhů, abyste vstoupil do svazku naší
armády. Totiž samozřejmě jen do vyšší technické služby, která leží ve směru
vaší práce, a to hned v hodnosti abych tak řekl… Chci říci, že není sice
naprosto zvykem vojensky aktivovati – krom případu války – civilní odborníky,
ale v našem případě – vzhledem k tomu, že přítomná situace si nezadá mnoho s
válkou – se zvláštním zřením k vašemu vskutku mimořádnému, přítomnými poměry
ještě více pointovanému významu, a… se zcela ojedinělým ohledem k vašemu
výjimečnému postavení, nebo přesněji řečeno k vašim… v nejvyšší míře soukromým
závazkům –“
„K jakým závazkům?“ přerušil ho Prokop chraptivě.
„Nu,“ zabreptal Carson trochu zmaten, „myslím… váš zájem, váš poměr…“
„Já jsem se vám z žádného zájmu nezpovídal,“ odbyl ho Prokop příkře.
„Haha,“ spustil pan Carson jaksi osvěžen touto hrubostí, „to se ví že ne;
nebylo taky třeby. Holenku, s tím jsme se taky tam nahoře neoháněli, co? To se
rozumí že ne. Prostě osobní ohledy a tečka. Vlivná intervence, víte? Ke všemu
jste dokonce cizozemec – Ostatně i to je vyřízeno,“ dodal honem. „Stačí, když
podáte žádost o udělení našeho státního občanství.“
„Aha.“
„Chtěl jste říci?“
„Nic, jen aha.“
„Aha. Tak to je všecko, ne? Stačí tedy podat formální žádost a… mimoto… Nu,
chápete přece, že… že je třeba jisté záruky, ne? Prostě si něčím vysloužíte
hodnost, která se vám udělí… za mimořádné zásluhy, že ano? Předpokládá se, že…
že vydáte armádní správě… rozumíte, že vydáte…“
Bylo hrozné ticho. Le bon prince se díval z okna, Carsonovy oči zmizely za
blýskavými skly; a Prokopovi se svíralo srdce úzkostí.
„… že totiž vydáte… prostě vydáte…,“ koktal Carson stěží dýchaje napětím.
„Co?“
Carson napsal prstem do vzduchu veliké K. „Nic víc,“ vydechl odlehčen. „Den
nato dostanete dekret… jmenován extra statum setníkem-inženýrem sapérů…
přidělen do Balttinu. Hotovo. Tak.“
„Totiž jen prozatím setníkem,“ ozval se oncle Charles. „Víc jsme nedocílili.
Ale dostalo se nám záruky, že jakmile dojde neočekávaně k válce –“
„Tedy do roka,“ vyhrkl Carson, „nanejvýš do roka.“
„– jakmile dojde k válce – ať je to kdy chce a s kým chce –, budeš jmenován
generál-inženýrem zákopnictví… v hodnosti generála jízdy, a změní-li se snad –
po výsledku války – vládní forma, bude s tím spojen titul Excelence a… zkrátka
nejprve baronie. I v tom směru… se nám dostalo… nejvyššího ujištění,“ dokončil
Rohn nehlasně.
„A kdo vám řekl, že bych to chtěl?“ ozval se Prokop ledově.
„Ale můj bože,“ vysypal Carson, „kdo by to nechtěl? Mně slíbili titul rytíře;
já na to sic kašlu, ale není to pro mne, je to pro svět. Ostatně pro vás by to
mělo docela zvláštní význam.“
„Tak vy tedy myslíte,“ děl Prokop pomalu, „že vám přece jen vydám Krakatit?“
Pan Carson chtěl vyletět, ale oncle Charles jej zadržel. „Máme za to,“ začal
vážně, „že učiníš vše nebo že… případně… přineseš každou oběť, abys zachránil
princeznu Hagenovou z jejího nelegálního a… nesnesitelného postavení. Za
zvláštních okolností… může princezna podat ruku vojákovi. Jakmile budeš
setníkem, upraví se váš poměr… přísně tajným zasnoubením; princezna ovšem
odjede a vrátí se, až… až bude moci požádati člena panujícího domu, aby byl
jejím svědkem při svatbě. Do té doby… do té doby je na tobě, aby sis vysloužil
manželství, jakého jsi hoden a jakého je hodna princezna. Podej mi ruku.
Nemusíš se ještě rozhodovat; rozvaž dobře, co činíš, co je tvá povinnost a co
máš za ni dát. Mohl bych se dovolávat tvé ctižádosti; ale mluvím jen k tvému
srdci. Prokope, ona trpí nad své síly a přinesla lásce větší oběť než kdy
která žena. I ty jsi trpěl; Prokope, ty trpíš ve svém svědomí; ale nedělám na
tebe nátlak, protože ti věřím. Važ dobře, a potom mně povíš…“
Pan Carson pokyvoval hlavou skutečně a hluboce dojat. „Je to tak,“ řekl. „Jsem
sic sprostá kůže, taková stará hovězí juchta, ale musím říci, že… že… Já vám
to říkal, ta ženská má rasu. Kristepane, to člověk teprve vidí…“ Uhodil se
pěstí do srdeční krajiny a pohnutě mrkal. „Člověče, já bych vás zaškrtil,
kdybyste… kdybyste nebyl hoden…“
Prokop už neposlouchal; vyskočil a pobíhal po pokoji s tváří svraštělou a
rozlícenou. „Já… já tedy musím, že?“ drtil chraptivě. „Tak tedy já musím?
Dobře, když musím… Vy jste mne zaskočili! Já přece nechtěl –“
Oncle Rohn vstal a položil mu mírně ruku na rameno. „Prokope,“ řekl,
„rozhodneš se sám. Nepospícháme na tebe; poraď se s tím nejlepším, co v tobě
je; dotaž se Boha, lásky nebo svědomí nebo cti nebo já nevím čeho. Jen
pamatuj, že nejde jen o tebe, ale i o tu, která tě miluje tak, že je s to…
učinit…“ Mávl bezmocně rukou. „Pojďme!“


XLIV.

Ten den byl podmračný a sychravý. Princezna pokašlávala, mrazilo ji a opět
hořela, ale nevydržela v posteli: čekala odpověď Prokopovu. Vyhlížela oknem,
nevyjde-li ven, a zase zavolala Paula. Stále totéž: pan inženýr přechází po
pokoji. A neříká nic? Ne, nic neříká. Vlekla se od stěny ke stěně, jako by ho
chtěla provázet; a zas usedla a kolébala se celým tělem, aby opila svůj
zimničný nepokoj. Oh, ani to už nelze snést! Zničehonic mu začala psát dlouhý
dopis; zapřísahala ho, aby si ji vzal za ženu; že nemusí vydat nic, žádné své
tajemství, žádny Krakatit; že ona za ním půjde do jeho života a bude mu
sloužit, děj se co děj. „Miluji Tě tak,“ psala, „že mně už žádná oběť nestačí,
abych Ti ji přinesla. Podrob mne zkoušce, zůstaň chudý a neznámý; půjdu s
Tebou jako Tvá žena, a nikdy již se nebudu moci vrátit do světa, který
opustím. Vím, že mne miluješ málo a jen roztržitým koutkem srdce; ale zvykneš
si na mne. Byla jsem pyšná, zlá a vášnivá; změnila jsem se, chodím mezi
starými věcmi jako cizí, přestala jsem být –“ Přečtla to a roztrhala to na
kousky tiše sténajíc. Byl večer, a od Prokopa zpráva nepřicházela.
Snad se ohlásí sám, napadlo ji, a v netrpělivém chvatu se dala oblékat do
večerních šatů. Rozčilena stála před velikým zrcadlem a zkoumala se palčivýma
očima, děsně nespokojena s účesem, se šaty, se vším možným; pokrývala
rozpálené líce novými a novými vrstvami pudru, mrazilo ji na nahých pažích,
ověšovala se šperky; připadala si ošklivá, nemožná a nemotorná. „Nepřišel
Paul?“ ptala se každou chvíli. Konečně přišel: nic nového, pan Prokop sedí
potmě a nedovolí rozsvítit.
Je už pozdě; princezna nesmírně unavena sedí před zrcadlem, pudr jí odprýskává
s rozžhavených lící, je zrovna šedivá a ruce má ztuhlé. „Odstroj mne,“ káže
chabě komorné. Svěží, telátkovité děvče snímá s ní šperk za šperkem, rozpíná
šaty a navléká jí průsvitný peignoir; a právě když se chystá pročesat její
rozpoutanou hřívu, vrazí do dveří Prokop neohlášen.
Princezna strnula a zbledla ještě víc. „Jdi, Marieke,“ vydechla a sevřela na
hubených prsou peignoir. „Proč… jsi… přišel?“
Prokop se opíral o skříň, velmi bledý a s očima načisto krvavýma. „Tak tedy,“
vyrazil zaškrceně, „to byl váš plán, že? To jste na mne dobře nastrojili!“
Vstala jako udeřena: „Co – co – co to mluvíš?“
Prokop zaskřípal zuby. „Já vím, co mluvím. Tedy o to tady šlo: abych… abych
vám vydal Krakatit, že? Oni chystají válku, a vy, vy,“ zařval tlumeně, „vy
jste jejich nástroj! Vy i s vaší láskou! Vy i s vaším manželstvím, vy špiónko!
A já, já měl být chycen na lep, abyste zabíjeli, abyste se mstili –“
Svezla se na kraj židle s očima úděsně vytřeštěnýma; celým tělem jí zalomcoval
strašný suchý vzlyk; chtěl se k ní vrhnout, ale zadržela ho posuňkem ztuhlé
ruky.
„Kdo vůbec jste?“ drtil Prokop. „Jste princezna? Kdo vás zjednal? Považ,
ničemná, žes chtěla povraždit tisíce tisíců; žes pomáhala tomu, aby byla
rozmetena města a aby náš svět, náš, a ne váš, svět nás lidí byl rozbit!
Rozbit, roztříštěn, zavražděn! Proč jsi to učinila?“ křičel a svezl se na
kolena plaze se k ní. „Co jsi to chtěla učinit?“
Zvedla se s tváří plnou hrůzy a odporu a couvla před ním. Položil tvář na
místo, kde seděla, a rozplakal se těžkým, hrubým chlapským vzlykáním. Užuž by
klekla vedle něho; ale přemohla se a ustoupila ještě dál, tisknouc k prsoum
ruce zkřivené křečí. „Tedy tohle,“ šeptala, „tohle ty myslíš!“
Prokop se dusil lítou bolestí. „Víš ty,“ křičel, „co je to válka? Víš, co je
Krakatit? Nikdy tě nenapadlo, že já jsem člověk? A – a – já vás nenávidím!
Proto tedy jsem vám byl dobrý! A kdybych byl Krakatit vydal, bylo by najednou
po všem; princezna by ujela a já, já –“ Vyskočil tluka se pěstmi do hlavy. „A
já to už chtěl udělat! Milión životů za – za – za – Co, ještě málo? Dva
milióny mrtvých! Deset miliónů mrtvých! to – to – to už je partie i pro
princeznu, že? To už stojí za to se trochu zahodit! Já blázen! Aaá,“ zavyl,
„fuj! Já se vás děsím!“
Byl hrozný a netvorný s pěnou kolem úst, tváří naběhlou a očima jako šílenec,
těkajícíma v nystagmu nepříčetnosti. Tiskla se ke zdi zsinalá a vytřeštěná, se
rty zkřivenými děsem. „Jdi,“ zaúpěla, „jdi odtud!“
„Neboj se,“ chraptěl, „já tě nezabiju. Vždycky jsem se tě děsil; a i když – i
když jsi byla má, hrozil jsem se a nevěřil jsem ti – ani po vteřinu. A přece,
přece jsem tě – Já tě nezabiju. Já – já vím dobře, co dělám. Já – já –“ Hledal
něco, popadl láhev s kolínskou vodou, nalil si celou záplavu na ruce a myl si
čelo. „Ahah,“ vydechl, „ahaha. Ne-boj se! Ne – ne –“
Uklidnil se jaksi, klesl na židli a položil hlavu do dlaní. „Tedy,“ začal
chraptivě, „tedy – tedy si můžeme pohovořit, že? Vidíte, jsem klidný. Ani… ani
prsty se mi netřesou…“ Vztáhl ruku, aby to ukázal; třásla se, až hrozno se
podívat. „Můžeme… nerušeně, že? Jsem už docela klidný. Můžete se ustrojit.
Tedy… váš strýček mi řekl, že… že jsem povinen… že je věc cti, abych vám
umožnil… napravit… napravit poklesek, a tedy že musím… prostě musím… si
vysloužit titul… prodat se, a tím zaplatit… oběť, kterou jste –“
Vzchopila se smrtelně bledá, aby něco řekla. „Počkejte,“ zarazil ji. „Ještě
jsem ne – Vy všichni jste mysleli… a máte své pojmy o cti. Tedy jste se
strašně mýlili. Já nejsem kavalír. Já jsem… syn ševce. Na tom nezáleží, ale…
já jsem pária, rozumíte? Nízký a ničemný chlap. Já nemám žádnou čest. Můžete
mne vyhnat jako zloděje, nebo mne odvézt na pevnost. Já to neudělám. Nedám
Krakatit. Můžete si myslet… že jsem třeba tak podlý. Mohl bych vám povídat… co
si myslím o válce. Já byl ve válce… a viděl jsem dusivé plyny… a vím, co lidé
dovedou. Já nedám Krakatit. Nač bych vám to vykládal? Tomu vy nerozumíte; jste
prostě tatarská princezna, a příliš nahoře… Chci vám jenom říci, že to
neudělám, a že poníženě děkuju za čest – Ostatně jsem i zasnouben; neznám jí
sice, ale zasnoubil jsem se jí – To je má další ničemnost. Lituji, že jsem…
vůbec nebyl hoden vaší oběti.“
Stála jako zkamenělá, zarývajíc nehty do zdi. Bylo kruté ticho, jen škrabání
jejích nehtů skřípělo v nesnesitelném mlčení.
Zvedl se těžce a pomalu: „Chcete něco říci?“
„Ne,“ vydechla a její ohromné oči úporně tkvěly v prázdnu. Byla chlapecky útlá
v rozevřeném peignoiru; byl by se svezl na zem, aby políbil její drkotající
kolena.
Přiblížil se k ní spínaje ruce. „Princezno,“ řekl sevřeně, „teď mne odvezou…
pod titulem špióna nebo jak. Nebudu se už bránit. Děj se co děj; jsem
připraven. Vím, že vás už neuvidím. Neřeknete mně nic na cestu?“
Rty se jí chvěly, ale nepromluvila; ó bože, nač se to dívá tam do prázdna?
Přistoupil až k ní. „Miloval jsem vás,“ vypravil ze sebe, „miloval jsem vás
víc, než jsem dovedl říci. Jsem nízký a hrubý člověk; ale teď vám mohu říci,
že… že jsem vás miloval jinak… a víc. Bral jsem vás… svíral jsem vás z
úzkosti, že nejste má, že mi uniknete; chtěl jsem se ujistit… Nikdy jsem tomu
nemohl věřit; a proto jsem –“ Nevěda, co činí, položil jí ruku na rameno;
zachvěla se pod teninkou látkou peignoiru. „Miloval jsem vás… jako zoufalec…“
Obrátila k němu oči. „Milý,“ zašeptala, a bledou lící jí prokmitla matná vlna
krve. Sklonil se rychle a políbil její prýskající rty; nebránila se.
„Jak to, jak to,“ zaskřípěl zuby, „že tě i teď miluju?“ Divými tlapami ji
odtrhl od stěny a sevřel; zazmítala sebou tak zběsile, že kdyby povolil,
skácela by se na zem; i zachytil ji pevněji sám kolísaje jejím zdivočelým
odporem. Svíjela se se zuby zaťatými a rukama křečovitě vzepřenýma o jeho
prsa; vlasy jí padly přes tvář, hryzala se do nich, aby potlačila křik,
odstrkovala ho od sebe zlomena v pase a převalujíc se jako v padoucnici. Bylo
to nesmyslné a ohavné; měl jediné vědomí, že ji nesmí pustit na zem a že nesmí
porazit židli; a že… že… co by si počal, kdyby se mu vymkla? – že by se pak
hanbou musel propadnout. Strhl ji k sobě a zaryl se rty do její rozpoutané
kštice; našel rozpálené čelo; odvracela odporem hlavu a zoufale se snažila
uvolnit svěrák jeho paží.
„Dám, dám Krakatit,“ slyšel trna svůj vlastní hlas. „Dddám, slyšíš? Všecko
dám! Válku, novou válku, nové milióny mrtvých. Mně – mně – mně je vše jedno.
Chceš? Řekni jen slovo – Říkám ti přece, že dám Krakatit! Přísahám, já – já ti
pří-sss – Miluju tě, slyšíš? Ať – ať – ať už se děje co chce! A – a – kdyby
měl zajít celý svět – Já tě miluju!“
„Pusť,“ zakvílela lomcujíc sebou.
„Nemohu,“ sténal ponořen tváří v její vlasy. „Jsem nejbídnější člověk. Zra-
zradil jsem celý svět, celý lidský svět. Naplij mně do tváře, ale ne-vy-háněj
mne! Proč tě nemohu pustit? Dám Krakatit, slyšíš? přísahal jsem to; ale teď,
teď mne nech zapomenout! Kde – kde – kde máš ústa? Jsem podlec, ale lllíbej
mne! Jsem ztra-ztra–“
Zakolísal, jako by měl padnout; nyní se mu mohla vymknout, tápal rukama do
prázdna; tu zvrátila hlavu, přehodila vlasy nazad a nastavila mu rty. Vzal ji
do rukou, ztuhlou a pasívní; líbal její sevřená ústa, palčivé líce, krk, oči;
vzlykal chraptivě, nebránila se, nechala se přímo nést. Zděsil se její nehybné
trpnosti a pustil ji couvaje. Zapotácela se, přejela si dlaní čelo, usmála se
uboze – byl to strašlivě žalný pokus o úsměv – a vzala ho kolem krku.


XLV.

Bděli přimknuti k sobě a s očima vytřeštěnýma do polotmy. Cítil její srdce
horečně bíti; nepromluvila slova za ty hodiny, líbala ho nenasytně a opět se
odtrhla, kladla šáteček mezi své a jeho rty, jako by se bála na něho dechnout;
i nyní odvrací tvář a zírá horečně do tmy –
Posadil se objímaje si kolena. Ano, ztracen; chycen na vějičku, spoután, upadl
v ruce Filištínů. A nyní se děj, co se díti musí. Vydáš zbraň v ruce těch, kdo
jí užijí. Tisíce tisíců zahynou. Tak tedy pohleď, není-li to před tebou
nesmírné pole trosek? Toto byl kostel a toto dům; toto byl člověk. Strašná je
síla a všechno zlé je z ní. Buď zlořečena síla, duše zlá a nevykoupená. Jako
Krakatit, jako já, jako já sám.
Tvořivá, pilná slabosti lidská, z tebe je všechno dobré a poctivé dílo; tvá
práce je vázat a spojovat, slučovat části a udržovat, co je spojeno. Ať je
prokleta ruka, která rozpoutá sílu! Ať je zlořečen, kdo poruší svazky živlů!
Všecko lidské je jenom lodička na oceánu sil; a ty, ty rozpoutáš bouři, jaké
dosud nebylo –
Ano, já rozpoutám bouři, jaké dosud nebylo; vydám Krakatit, živel rozvázaný, a
roztříští se lodička lidstva. Tisíce tisíců zahynou. Budou vyhlazeny národy a
smetena města; nebude mezí tomu, kdo má v ruce zbraň a zkázu v srdci. Ty jsi
to učinil. Strašná je vášeň, Krakatit lidských srdcí; a všechno zlé je z ní.
Pohlédl na princeznu – bez nenávisti, rván neklidnou láskou a soucitem. Nač
nyní myslí, ztuhlá a vyjevená? Sklonil se a políbil ji na rameno. Za tohle
tedy vydám Krakatit; vydám jej a odejdu odtud, abych už neviděl hrůzu a hanbu
své porážky. Zaplatím strašlivou cenu za svou lásku, a odejdu –
Bezmocně sebou trhl: Cožpak mě nechají odejít? Co by jim byl platen Krakatit,
pokud jej mohu vyzradit jiným? Aá, proto mne chtějí navěky svázat! Aá, proto
jim musím vydat duši i tělo! Tady, tady zůstaneš, spoután vášní, a věčně se
budeš hroziti této ženy; budeš sebou zmítat v zlořečené lásce, a vymýšlet
budeš pekelné zbraně… a sloužit jim budeš…
Obrátila se k němu bezdechým pohledem. Seděl bez hnutí a po hrubé, těžkotvaré
líci mu stékaly slzy. Zvedla se na lokty a pohlížela na něho utkvělýma,
bolestně zkoumavýma očima; nevěděl o tom, přivíral oči a trnul v tuposti
porážky. Tu vstala tichounce, rozsvítila u toaletního stolku a počala se
strojit.
Vytrhl se teprve cvaknutím odloženého hřebene. Pohlížel na ni s údivem, jak
oběma rukama zvedá a stáčí rozhozenou kštici. „Zítra… zítra to odevzdám,“
šeptal. Neodpověděla, měla vlásničky mezi rty a rychle svíjela vlasy v tuhou
přilbu. Sledoval každé její hnutí; spěchala zimničně, opět se zarážela a
hleděla k zemi, pak zase přikývla hlavou a strojila se tím rychleji. Nyní se
zvedá, pohlíží na sebe zblízka, pozorně do zrcadla, přejede tvář pudrem: jako
by tu nikdo nebyl. Odchází do vedlejšího pokoje a vrací se oblékajíc si přes
hlavu sukni. Opět usedá a přemýšlí komihajíc trupem; pak přikývne hlavou a
zajde do vedlejší garderoby.
Vstal a šel potichu k jejímu toaletnímu stolku. Bože, co je tu věcí divných a
něžných! Flakónky, tyčinky, pouzdra, krémy, hračiček bezpočtu; to tedy je
řemeslo žen; oči, úsměv, vůně, vůně ostrá a lichotná – Pahýly jeho prstů se
třásly na těch křehkých a tajemných věcičkách, podrážděny, jako by se dotýkaly
něčeho zakázaného.
Vstoupila do dveří v koženém kabátci a kožené přilbě na hlavě a oblékala
veliké rukavice. „Připrav se,“ řekla bezbarvě, „pojedeme.“
„Kam?“
„Kam chceš. Připrav si, co potřebuješ, ale pospěš, pospěš!“
„Co to znamená?“
„Neptej se dlouho. Tady už zůstat nemůžeš, víš? Oni tě jen tak nepustí.
Pojedeš?“
„Na… na jak dlouho?“
„Navždycky.“
Srdce mu zabouchalo. „Ne – ne – já nepojedu!“
Přistoupila k němu a políbila ho na tvář. „Musíš,“ řekla tiše. „Já ti to
povím, až budeme venku. Přijď před zámek, ale brzo, dokud je tma. Jdi, jdi
teď!“
Jako ve snách šel do svého pokoje; shrábl své papíry, své drahocenné a
nedokončené zápisy, a rychle se rozhlédl: Je to všecko? Ne, nepojedu, blesklo
mu hlavou, i nechal papíry ležet a běžel ven. Stálo tam veliké, nerozsvícené,
tlumeně hrčící auto; princezna už seděla u volantu. „Rychle, rychle,“ šeptala.
„Jsou vrata otevřena?“
„Jsou,“ bručel rozespalý šofér zavíraje kapot vozu.
Nějaký stín obcházel zpovzdálí automobil a stanul ve tmě.
Prokop přistoupil k otevřeným dvířkám vozu. „Princezno,“ mručel, „já… jsem se
rozhodl, že… vydám vše… a… zůstanu.“
Neposlouchala ho; nakloněna kupředu zírala upřeně na místo, kde onen stín
splynul s tmou. „Rychle,“ vydechla najednou, chopila Prokopa za ruku a vtáhla
ho do vozu vedle sebe; a již se jediným pohybem páky auto rozjelo. V tu chvíli
se rozsvítilo v zámku nějaké okno, a onen stín se vyřítil ze tmy. „Stát,“
křikl a vrhl se před vůz; byl to Holz.
„Z cesty,“ vykřikla princezna, zavřela oči a zapnula na plnou rychlost. Prokop
zvedl ruce zděšením; tu již zařičel nelidský řev, kolo se přehouplo přes něco
měkkého, Prokop chtěl vyskočit, ale vtom sebou auto smýklo stranou v ohybu
vrat, až se dvířka sama zabouchla, a šíleně letělo do tmy. S hrůzou se otočil
k princezně; stěží ji rozeznal v kožené přilbě, skloněnou tváří až nad volant.
„Co jste to udělala?“ vyrazil.
„Buď tiše,“ sykla ostře a stále se tak nakláněla vpřed. Rozeznal v dálce tři
postavy na bledé silnici; zpomalila a zastavila těsně u nich. Byla to vojenská
hlídka. „Proč nemáte rozsvíceno?“ huboval jeden voják. „Kdo je?“
„Princezna.“
Vojáci zvedli ruce k čepicím a ustoupili. „Heslo?“
„Krakatit.“
„Račte rozsvítit. Koho račte mít s sebou? Prosím, povolení.“
„Hned,“ řekla princezna klidně a zasunula na prvou. Auto vyrazilo přímo
skokem; vojáci stěží uskočili. „Nestřílet,“ křikl jeden, a vůz letěl do tmy.
Na zatáčce rychle zahnula a jela skoro zpátečním směrem. Zastavila hladce před
šraňky zavírajícími silnici. Dva vojáci se blížili k vozu.
„Kdo má službu?“ ptala se suše.
„Poručík Rohlauf,“ hlásil voják.
„Zavolat!“
Poručík Rohlauf vyběhl ze strážnice zapínaje se.
„Dobrý večer, Rohlaufe,“ řekla přívětivě. „Jak se máte? Prosím, nechte mi
otevřít.“
Stál tu uctivě, ale nedůvěřivě měřil Prokopa: „Velmi rád, ale… má pán
povolení?“
Princezna se zasmála. „To je jen sázka, Rohlaufe. Za pětatřicet minut na
Brogel a zpátky. Nevěříte? Přece mi nezkazíte sázku.“ Podala mu z vozu ruku,
strhnuvši rychle rukavici. „Na shledanou, ano? Ukažte se zas někdy.“ Srazil
paty a políbil jí ruku hluboce se klaně; vojáci otvírali šraňky a vůz se
rozjel. „Na shledanou!“ volala nazpět.
Řítili se nekonečnou alejí silnice. Tu a tam mihlo se lidské světélko, ve vsi
zaplakalo dítě, pes za plotem běsnil za temným letícím vozem. „Co jste
učinila,“ křičel Prokop. „Víte, že Holz má pět dětí a sestru mrzáčka? Jeho
život… je desetkrát víc než můj i váš! Co jsi učinila?“
Neodpovídala; se svraštělým čelem a zuby zaťatými dávala pozor na cestu,
pozvedajíc se chvílemi, aby lépe viděla. “Kam chceš?“ zeptala se najednou na
rozcestí vysoko nad spícím krajem.
„Do pekla,“ zaskřípěl.
Zastavila vůz a obrátila se k němu vážně: „Neříkej to! Copak myslíš, že jsem
neměla už stokrát chuť nás oba rozbít na nějaké zdi? Nemysli si, šli bychom
oba do pekla. Já teď vím dobře, že je peklo. Kam chceš jet?“
„Chci… být s tebou.“
Zavrtěla hlavou. „To nejde. Nevíš už, co jsi říkal? Jsi zasnouben a… chceš
zachránit svět před něčím hrozným. Tak to udělej. Ty musíš mít čisto sám v
sobě; jinak… jinak jsi zlý. A já už nemohu…“ Hladila rukou volant. „Kam chceš
jet? Kde vůbec jsi doma?“
Sevřel ji vší silou v zápěstí. „Za-zabilas Holze! Copak nevíš –“
„Vím,“ řekla tiše. „Myslíš, že jsem to necítila? To ve mně tak zachrustěly
kosti; a pořád ho vidím před sebou, a já pořád, pořád vozem do něho, a zas mně
běží do cesty –“ Zachvěla se. „Tak kudy? Vpravo nebo vlevo?“
„Tedy je konec?“ ptal se tiše.
Pokývla hlavou. „Tedy je konec.“
Otevřel dvířka, vyskočil z vozu a postavil se před kola. „Jeď,“ řekl
chraptivě. „Pojedeš přese mne.“
Ujela s vozem dva kroky zpět. „Pojď, musíme dál. Dovezu tě aspoň blíž k
hranicím. Kam chceš?“
„Zpátky,“ skřípěl zuby, „zpátky s tebou.“
„Se mnou není… ani dopředu, ani zpátky. Copak mi nerozumíš? Musím to udělat,
abys viděl, aby bylo jisto, že jsem tě měla ráda. Myslíš, že bych mohla ještě
jednou slyšet, cos mi řekl? Zpátky nemůžeš; buď bys musel vydat to… co nechceš
a nesmíš, nebo by tě odvezli, a já –“ Spustila ruce do klína. „Vidíš, i na to
jsem myslela, že bych šla s tebou… dopředu. Dovedla bych to, dovedla bych to
jistě; ale – Ty jsi tam někde zasnouben; jdi k ní. Hleď, nikdy mě nenapadlo
ptát se tě na to. Když je člověk princezna, myslí si, že je na světě sám. Máš
ji rád?“
Pohlédl na ni utrýzněnýma očima; přece jen nedovedl zapřít –
„Tak vidíš,“ vydechla. „Ty neumíš ani lhát, ty milý! Ale pochop, když jsem si
to pak srovnala v hlavě – Co jsem ti byla? Co jsem to dělala? Myslel jsi na
ni, když jsi mne miloval? Jak ses mne musel hrozit! Ne, neříkej nic; neber mi
sílu říci ti to poslední.“
Zalomila rukama. „Já tě milovala! Já jsem tě milovala, člověče, že – že jsem
mohla cokoliv – a ještě víc – Ale ty, tys o tom pochyboval tak děsně, žes
nakonec zlomil i mou víru. Miluju tě? Já nevím. Mohla bych si rýt nožem v
prsou, když tě tu vidím, a zemřít bych chtěla a já nevím co, ale miluju tě? Já
– já už nevím. A když jsi mne… naposledy… vzal do rukou, cítila jsem… něco
nedobrého v sobě… i v tobě. Setři mé polibky; byly… byly… nečisté,“ vydechla
bezhlase. „Musíme se rozejít.“
Nedívala se na něho, neslyšela, co odpovídá; a hle, nyní se jí chvějí víčka,
pod nimi se dělá slza, vyhrkne, kane rychle, zastaví se, a pak ji dohoní
druhá. Plakala beze zvuku, s rukama na volantu; a když se chtěl přiblížit,
popojela kousek zpět.
„Už nejsi Prokopokopak,“ šeptala, „jsi nešťastný, nešťastný člověk. Viď, trháš
sebou na řetěze… jako já. Bylo to… nedobré pouto, co nás svázalo; a přece,
když to člověk přetrhává, je mu… je mu, jako by celé nitro šlo s sebou, i
srdce, i duše… Bude v člověku čisto, když zůstane tak prázdný a pustý?“ Slzy
jí vyhrkly prudčeji. „Milovala jsem tě, a teď už tě neuvidím. Jdi, jdi mi z
cesty, já otočím.“
Nehnul se, jako zkamenělý. Pojela těsně k němu. „Sbohem, Prokope,“ řekla tiše,
a počala pozpátku sjíždět po silnici. Rozběhl se za ní; tu sjížděla couvajíc
vozem rychleji, rychleji, stále rychleji; bylo to, jako by se propadala.


XLVI.

Stanul a naslouchal trna hrůzou, zda nezaslechne praskot vozu roztříštěného
někde v zákrutu silnice. Není to výbušné hučení motoru z dálky? Není to děsné
a smrtelné ticho konce? Bez sebe uháněl Prokop po silnici za ní. Seběhl
serpentinou dolů, k patě svahu; ani památky po voze. Utíkal opět nahoru,
pátral po úbočích, slézal drásaje si ruce, kde rozeznával něco temného nebo
světlého; bylo to hloží nebo kamení, a tu opět klopýtal a drápal se na silnici
a zarýval se očima do tmy, zda… zda není někde hromada trosek, a pod ní…
Byl opět nahoře u rozcestí; právě tady se začala propadat do tmy. Usedl na
milník. Ticho, nesmírné ticho. Studené hvězdy popůlnoční, letí teď někde temný
meteor vozu? Což se nic neozve, nezavolá pták, neštěkne ve vsi pes, nedá nic
znamení života? Vše ustrnulo slavnostním mlčením smrti. A toto je tedy konec,
tichý a mrazivý a temný konec všeho; prázdnota vykroužená tmou a tichem;
prázdnota stojatá a ledová. Do kterého kouta se mám skrýt, abych jej vyplnil
svou bolestí? Kéž byste se zamžily, kéž by byl konec světa! Rozštípne se země,
a v rachotu síly promluví Pán: Beru tě zpět, tvore bolestný a slabý; nebylo
čisto v tobě, a nedobré síly jsi rozpoutal. Milý, milý, ustelu ti lůžko z
nicoty.
Prokop se počal třásti pod trnovou korunou vesmíru. A tedy ničím není utrpení
člověka a nemá ceny; je maličké a schoulené, třesoucí se bublinka na dně
prázdnoty. Dobře, dobře, pravíš, že svět je nesmírný; ale já kéž zemru!
Na východě pobledla nebesa, chladně prosvitá silnice a bílé kameny; hleď,
stopy kol, stopy v mrtvém prachu. Prokop se zvedá ztuhlý a omámený, a dává se
na pochod. Tam dolů, směrem k Balttinu.
Putoval bez zastávky. Tady je vesnice, alej jeřabin, můstek přes tichou a
temnou řeku; zvedá se mlha a zastře slunce; a opět je šedý a chladný den,
červené střechy, červená stáda krav. Jak může být daleko do Balttinu? Šedesát
sedmdesát kilometrů. Suché listí, samé suché listí.
Po poledni usedl na hromádku štěrku; nemohl již dále. Jede tudy selský vozík;
sedlák zastavil a koukal na zhrouceného člověka. „Nechcete se svézt?“ Prokop
vděčně přikývl a beze slova usedl k němu. A pak vozík zastavil v městečku.
„Tak, tady jsme,“ řekl sedlák. „Kam vlastně jdete?“ Prokop slezl a putoval
dál. Jak může být do Balttinu daleko?
Začíná pršet; ale Prokop již nemůže dál a usedá na pažení mostu; dole se
vzteká a pění studený potok. Z protější strany letí auto, zpomalí na mostě a
zastaví; z něho vyskočí pán v kozím kožiše a míří k Prokopovi. „Kde se tu
berete?“ Je to pan ďHémon, na tatarských očích má automobilové brýle, vypadá
jako obrovský huňatý brouk. „Jedu z Balttinu, hledají vás.“
„Jak je daleko do Balttinu?“ šeptá Prokop.
„Čtyřicet kilometrů. Co tam chcete? Vydali na vás zatykač. Pojďte, odvezu
vás.“
Prokop zavrtěl hlavou.
„Princezna odjela,“ povídá pan ďHémon tiše. „Dnes ráno, s oncle Rohnem.
Především, aby se zapomněla… jistá… nepříjemná věc s přejetým člověkem –“
„Je mrtev?“ vydechl Prokop.
„Dosud ne. A za druhé je princezna, jak snad víte, vážně tuberkulózní.
Odvážejí ji někam do Itálie.“
„Kam?“
„To nevím. Nikdo to neví.“
Prokop vstal a zavrávoral. „Tak tedy – tak tedy –“
„Pojedete se mnou?“
„Ne-nevím. Kam?“
„Kam chcete.“
„Já – já bych chtěl – do Itálie.“
„Pojďte.“ Pan ďHémon pomohl Prokopovi do vozu, hodil na něho kožišinu a
zabouchl dvířka. Vůz se rozjel.
A zas se rozvíjí krajina, ale divně, jakoby ve snu a pozpátku: městečko,
topolová alej, štěrk, můstek, korálové jeřabiny, vesnice. Vůz supaje stoupá
serpentinami do svahu, a zde je rozcestí, kde se rozloučili. Prokop se zvedá a
chce vyskočit z vozu; ale pan ďHémon jej strhl zpět, přišlápl pedál a zasunul
na čtvrtou. Prokop zavřel oči; nyní již nejedou po cestě, ale vznesli se do
vzduchu a letí; vítr ho pere do tváří, cítí mokré, hadrovité údery mračen,
výbuchy motoru splývají v táhlý a hluboký řev, dole se asi prohýbá země, ale
Prokop se bojí otevřít oči, aby zas neviděl letící aleje. Rychleji! zalknout
se! ještě rychleji! Obruč hrůzy a závrati mu stahuje prsa, nedýchá už a jektá
rozkoší šíleného řícení prostorem. Vůz klouže nahoru a dolů, někde pod nohama
se ozve křik lidí a zavytí psa, někdy se točí ležíce skoro na boku, jako by
jimi zakroužila smršť; a zas, zase rovný let, čirá rychlost, strašné a lomozné
tetelení drnčící tětivy dálek.
Otevřel oči. Je mlhavý soumrak, řady světel se protlačují šerem, tryskají
tovární světla. Pan ďHémon províjí vůz klubkem ulic, klouže předměstím
podobným zřícenině a opět vyrazí do polí. Vůz smýká před sebou dlouhá tykadla
světla, ohmatává trus, bláto, kameny, svistí v ohybech, vybuchuje bubnovou
palbou a vrhá se na dlouhý pás silnice, jako by jej navíjel. Vpravo a vlevo se
klikatí úzké údolí mezi horami, vůz se do něho zavrtává, zapadá v lesích,
šroubuje se s rachotem nahoru a střemhlav se spouští do nového údolí. Vesnice
vydechuje kotouče světla do husté mlhy, vůz proletí řiče a vrhaje za sebe
chuchvalce jisker, naklání se, klouže, krouží ve spirále nahoru, nahoru,
nahoru, přeskakuje něco a padá. Stop! zastavili v černé tmě; ne, je tu domek,
pan ďHémon bruče vystupuje, tluče na dvéře a hovoří s lidmi; po chvíli se
vrací s konví vody a nalévá ji do syčícího chladiče; v prudkém světle
reflektorů vypadá ve svém kožiše jako čert z dětské pohádky. Nyní obchází vůz,
ohmatává pneumatiky, zvedá kapot a něco povídá; a Prokop zdřímnul nesmírnou
únavou. Pak opět ho chopilo nekonečné rytmické otřásání; spal v koutě vozu a
nevěděl o ničem, po celé hodiny o ničem než o zmítavém kolébání; a procitl
teprve, když vůz zastavil před zářícím hotelem v ostrém horském vzduchu mezi
plochami sněhu.
Vzchopil se, zcela zdřevěnělý a rozlámaný. „To… to není Itálie,“ koktal
udiven.
„Ještě ne,“ řekl pan d,Hémon. „Ale teď se pojďte najíst.“ Vedl Prokopa
oslepeného tolika světly do oddělené jídelničky; bělostný ubrus, stříbro,
teplo, sklepník podobný velvyslanci. Pan ďHémon ani neusedl; přecházel po
jídelně a díval se na špičky prstů. Prokop tupě a rozespale se svezl na židli;
bylo mu k smrti jedno jíst nebo nejíst. Nicméně vypil horký bujón, porýpal se
v nějakých jídlech stěží vládna vidličkou, točil mezi prsty sklenkou vína a
spálil si útroby horoucí hořkostí kávy. Pan ďHémon vůbec neusedl; stále
přecházel po pokoji a v chůzi požil několik soust; a když byl Prokop hotov,
podal mu doutník a zapálil. „Tak,“ řekl, „a nyní k věci.“
„Od této chvíle,“ začal přecházeje, „budu pro vás prostě… kamarád Daimon.
Uvedu vás mezi naše lidi, není to daleko odtud. Nesmíte je brát příliš vážně;
jsou to zčásti desperados, štvanci a běženci smetení ze všech koutů světa,
zčásti fantasti, tlučhubové, diletanti spasitelství a doktrináři. Na jejich
program se nemusíte ptát; jsou jenom materiál, který nasadíme do naší hry.
Hlavní je, že vám můžeme dát k dispozici rozvětvenou a doposud tajnou
mezinárodní organizaci, která má všude své buňky. Jediný program je přímá
akce; na tu dostaneme všechny bez výjimky, beztoho po ní křičí jako po nové
hračce. Ostatně ,nová akční linie‘ a ,destrukce v hlavách‘ bude mít pro ně
neodolatelné kouzlo; po prvních úspěších půjdou za vámi jako ovce, zejména
odstraníte-li z vedení ty, které vám označím.“
Mluvil hladce jako zkušený řečník, totiž mysle přitom na něco jiného, a se
samozřejmou jistotou, jež vylučuje odpor a pochybnosti; Prokopovi se zdálo, že
už ho někdy slyšel.
„Vaše situace je jedinečná,“ pokračoval pořád chodě po pokoji. „Odmítl jste
nabídku jisté vlády; jednal jste jako rozumný člověk. Co vám mohou dát proti
tomu, co si můžete vzít sám? Byl byste blázen, abyste svou věc pustil z rukou.
Máte v hrsti prostředek, kterým můžete rozmetat všechny mocnosti světa. Já vám
poskytnu neomezený úvěr. Chcete padesát nebo sto miliónů liber? Můžete je mít
do týdne. Mně stačí, že jste dosud jediným majitelem Krakatitu. Devět a půl
deka je zatím v držení našich lidí, donesl jim to saský kamarád z Balttinu;
ale ti pitomci nemají ani ponětí o vaší chemii. Chovají to jako svátost v
porcelánové piksle a třikráte týdně se div neseperou o to, kterou vládní
budovu světa tím mají vyhodit do povětří. Ostatně je uslyšíte. Z té strany vám
tedy nehrozí nic. V Balttinu není ani špetky Krakatitu. Pan Tomeš je patrně v
koncích se svými pokusy –“
„Kde je Jirka – Jirka Tomeš?“ vypravil ze sebe Prokop.
„Prachárny Grottup. Už ho tam mají dost s jeho věčnými sliby. A kdyby se mu to
náhodou přece jen podařilo sestrojit, nebude mít z toho dlouho radost. Za to
vám stojím já. Zkrátka vy jediný máte Krakatit v moci a nevydáte jej nikomu.
Budete mít k dispozici lidský materiál a všechny naše organizační nitky. Já
vám dám tisk, který si platím. A konečně k vašim službám bude to, čemu se v
novinách říká ,tajemná rádiová stanice‘, totiž naše ilegální bezdrátové
spojení, které takřečenými antivlnami nebo extinkčními jiskrami přivádí váš
Krakatit k rozpadu do dálky dvou až tří tisíc kilometrů. To jsou vaše trumfy.
Dáte se do hry?“
„Co – co – co tím myslíte?“ ozval se Prokop. „Co s tím mám dělat?“
Kamarád Daimon stanul a hleděl upřeně na Prokopa. „Budete dělat, co chcete.
Budete dělat veliké věci. Kdo vám ještě může poroučet?“


XLVII.

Daimon přitáhl židli k Prokopovi a usedl.
„Ano,“ začal zamyšleně, „je to až nemožno chápat. Prostě v dějinách není
analogie moci, kterou vy máte v rukou. Budete dobývat světa s hrstkou lidí,
jako Cortez dobýval Mexika. Ne, ani to není pravý obraz. S Krakatitem a
stanicí držíte v šachu celý svět. Je to podivné, ale je to tak. Stačí hrst
bílého prášku, a v určenou vteřinu vyletí do povětří, co poručíte. Kdo tomu
může zabránit? Fakticky jste neobmezeným pánem světa. Budete udílet rozkazy,
aniž vás kdo viděl. Je to směšné: můžete odtud ostřelovat pro mne a za mne
Portugalsko nebo Švédsko; za tři za čtyři dny budou prosit o mír, a vy budete
diktovat kontribuce, zákony, hranice, co vás napadne. V tuto chvíli je jediná
velmoc; tou jste vy sám.
Myslíte, že přeháním? Mám tu velmi obratné chlapíky schopné všeho. Vyhlaste
pro švandu válku Francii. Někdy o půlnoci vyletí ministerstva, Banque de
France, pošta, elektrárna, nádraží a několik kasáren. Příští noci letiště,
arzenály, železniční mosty, muniční továrny, přístavy, majáky a silnice. Mám
zatím jen sedm letadel; můžete trousit Krakatit, kde vám libo; pak se zapne
stanice, a je to. Tak co, zkusíte to?“
Prokopovi bylo jako ve snu. „Ne! Proč bych to dělal?“
Daimon pokrčil rameny: „Protože můžete. Síla… se musí vybít. Má to za vás
udělat nějaký stát, když to můžete vykonat sám? Já nevím, co všechno můžete
provést; musí se začít, aby se to zkusilo; ručím vám za to, že tomu přijdete
na chuť. Chcete být samovládcem světa? Dobrá. Chcete svět vyhladit? Budiž.
Chcete jej obšťastnit tím, že mu vnutíte věčný mír, Boha, nový řád, revoluci
či co? Proč ne? Jen začněte, na programu nezáleží; nakonec budete dělat jen
to, co vám vnutí skutečnosti vámi vytvořené. Můžete rozbít banky, krále,
industrialism, vojska, věčné bezpráví nebo co je vám libo; však se pak ukáže,
co s tím bude dál. Začněte s čímkoliv; pak už to poběží samo. Jen nehledejte
analogie v dějinách, neptejte se, co smíte; vaše postavení je bezpříkladné;
žádný Čingischán nebo Napoleon vám nepovědí, co máte dělat a kde jsou vaše
meze. Nikdo vám nemůže poradit; nikdo se nemůže vžít do bezuzdnosti vaší moci.
Musíte být sám, chcete-li dojít až na kraj. Nikoho k sobě nepouštějte, kdo by
vám chtěl klást hranice nebo směr.“
„Ani vás, Daimone?“ ozval se Prokop ostře.
„Ani mne ne. Já stojím na straně síly. Jsem starý, zkušený a bohatý;
nepotřebuju nic, než aby se něco dělo a řítilo směrem, který určuje člověk. Mé
staré srdce se bude těšit tím, co budete provádět. Vymyslete si to
nejkrásnější, nejsmělejší a nejrajštější a uložte to světu právem své moci: ta
podívaná mne odmění za to, že vám sloužím.“
„Podejte mi ruku, Daimone,“ děl Prokop pln podezření.
„Ne, spálil bych vás,“ usmál se Daimon. „Mám starou, prastarou horečku. Co
jsem chtěl říci? Ano, jediná možnost síly je násilí. Síla je schopnost vnutit
věcem pohyb; neujdete nakonec tomu, aby se všechno netočilo kolem vás.
Zvykejte si na to předem; oceňujte lidi jen jako své nástroje nebo jako
nástroje myšlenky, kterou si vezmete do hlavy. Vy chcete nemožné dobro;
následkem toho budete asi velmi krutý. Nezastavujte se před ničím, chcete-li
prosadit veliké ideály. Ostatně i to přijde samo sebou. Zdá se vám nyní, že je
nad vaše síly, abyste – já nevím v jaké formě – vladařil na zemi. Budiž, ale
není to nad sílu vašich nástrojů; vaše moc dosahuje dále než každá střízlivá
rozvaha.
Zařídíte si své věci tak, abyste byl nezávislý na komkoliv. Ještě dnes vás dám
zvolit za předsedu zpravodajské komise; tím budete mít prakticky v rukou
extinkční stanici; ostatně je zařízena v objektu, jenž je mým soukromým
majetkem. Za chvíli uvidíte naše směšné kamarády; nepoplašte je žádnými
velkými plány. Jsou na vás připraveni a přijmou vás s nadšením. Promluvíte k
nim několik frází o blahu lidstva nebo co chcete; beztoho to zanikne v chaosu
názorů, kterému se říká politické přesvědčení.
Rozhodnete se sám, povedete-li první rány směrem politickým nebo hospodářským:
tedy budete-li nejdřív bombardovat vojanské objekty nebo továrny a trati.
První je efektnější, druhé zasahuje hlouběji. Můžete zahájit generální,
kruhový útok nebo si vyberete radiální sektor; zvolíte anonymní rozvrat nebo
veřejné a napohled šílené vyhlášení boje. Neznám vašeho vkusu; ostatně na
formě nezáleží, jen když projevíte svou moc. Jste nejvyšším soudcem světa;
odsuďte kohokoliv, naši lidé provedou váš rozsudek. Nepočítejte životů;
pracujete ve velkém, a celý svět má miliardy životů.
Hleďte, jsem průmyslník, novinář, bankéř, politik, vše, co chcete; zkrátka
jsem zvyklý počítat, ohlížet se na okolnosti a kramařit s omezenými šancemi.
Právě proto vám musím říci, a je to jediná rada, kterou vám dávám, než se
chopíte vlády: nepočítejte a neohlížejte se. Jakmile se jednou ohlédnete,
změníte se v plačící sloup jako žena Lotova. Já jsem rozum a číslo; hledím-li
vzhůru, chtěl bych se rozplynout v šílenství a v nespočetnosti. Vše, co je,
nevyhnutelně klesá z chaosu neomezenosti přes číslo k nicotě; každá velká síla
se staví proti tomuto sestupnému pádu; každá velikost chce se stát
nesmírností. Zahozena je síla, která se nepřelije přes staré hranice. Vám je
dána do rukou moc vykonat nesmírné věci; jste jí hoden či chcete s ní žabařit?
Já, starý praktik, vám pravím: myslete na šílené a bezměrné skutky, na rozměry
bezpříkladné, na nesmyslné rekordy lidské moci; skutečnost vám uškubne padesát
i osmdesát procent z každého velikého plánu; ale to, co zůstane, musí ještě
být nesmírné. Pokoušejte se o nemožné, abyste uskutečnil aspoň nějakou
neznámou možnost. Vy víte, jak velká věc je experiment; dobrá, všichni vladaři
světa se nejvíc děsí toho, že by to měli zkusit jinak, neslýchaně a obráceně;
nic není konzervativnější než lidské vládnutí. Vy jste první člověk na světě,
který může pokládat celý svět za svou laboratoř. Toto jest svrchované pokušení
na temeni hory: nedám ti vše, co je pod tebou, k požitku a rozkoši moci; ale
je ti to dáno, abys toho dobýval, abys to předělal a zkusil něco lepšího, než
je tento bídný a ukrutný svět. Světu je znovu a znovu třeba tvůrce; ale
tvůrce, který není svrchovaným pánem a vládcem, je jenom blázen. Vaše myšlenky
budou rozkazy; vaše sny budou dějinné převraty; a kdybyste nepostavil nic víc
než svůj pomník, stojí to za to. Přijměte, co je vaše.
A nyní půjdeme; čekají na nás.“


XLVIII.

Daimon spustil motor a skočil do vozu. „Hned tam budeme.“ Auto se sváželo s
Hory Pokušení do širokého údolí, letělo němou nocí, přesmyklo se přes pokojné
sedlo a stanulo před rozlehlým dřevěným domem mezi olšemi; vypadalo to jako
starý mlýn. Daimon vyskočil z vozu a vedl Prokopa k dřevěným schodům; ale tam
se jim postavil do cesty člověk s vyhrnutým límcem. „Heslo?“ ptal se. „I kuš,“
zahučel Daimon a strhl si automobilové brýle; člověk ustoupil a Daimon se hnal
nahoru. Vešli do veliké nízké jizby, jež byla jako školní světnice: dvě řady
lavic, pódium a katedra a tabule; jenomže tam bylo plno dýmu a výparů a křiku.
Lavice byly přeplněny lidmi s klobouky na hlavách; všichni se hádali, na pódiu
křičel rudovousý kolohnát, za katedrou stál suchý, pedantický stařík a zuřivě
zvonil.
Daimon šel rovnou k pódiu a skočil nahoru. „Kamarádi,“ křičel, a jeho hlas
zněl nelidsky jako hlas racka. „Přivedl jsem vám někoho. Kamarád Krakatit.“
Udělalo se ticho, Prokop se cítil uchopen a nešetrně omakáván padesáti páry
očí; jako ve snu vystoupil na pódium a rozhlédl se nevidomě po zamžené jizbě.
„Krakatit, Krakatit,“ hučelo to dole, a hukot stoupal ve křik: „Krakatit!
Krakatit! Krakatit!“ Před Prokopem stojí krásné rozcuchané děvče a podává mu
ruku: „Nazdar, kamaráde!“ Krátký horký stisk, vše slibující žeh očí, a už je
tu dvacet jiných rukou: hrubých, pevných i vysušených žárem, vlhce studených i
zduchovnělých; a Prokop se cítí zapnut v celý řetěz rukou, které si jej
podávají a přisvojují. „Krakatit, Krakatit!“
Pedantický stařík zvonil jako šílenec. Když to nic nepomohlo, vrhl se k
Prokopovi a potřásl mu rukou; měl ručičku vyschlou a kožnatou, jako z
pergamenu, a za ševcovskými brejličkami mu zářila ohromná radost. Dav zařval
nadšením a utišil se. „Kamarádi,“ promluvil stařík, „přivítali jste kamaráda
Krakatita… se spontánní radostí… se spontánní a živou radostí, která… které
dávám výraz také z předsednického místa. Vítám tě v našem středu, kamaráde
Krakatite. Vítáme také předsedu Daimona… a děkujeme mu. Žádám kamaráda
Krakatita, aby usedl… jako host… na předsednickém pódiu. Delegáti ať se
vysloví, mám-li dále říditi schůzi já… nebo předseda Daimon.“
„Daimon!“
„Mazaud!“
„Daimon!“
„Mazaud! Mazaud!“
„K čertu s vašimi formalitami, Mazaude,“ zahučel Daimon. „Předsedejte a dost.“
„Schůze pokračuje,“ křičel stařík. „Slovo má delegát Peters.“
Rudovousý člověk se ujal opět slova; jak se zdálo, útočil na anglickou Labour
Party, ale nikdo ho neposlouchal. Všechny oči se až hmotně opírají o Prokopa;
tamhle v koutě veliké, blouznivé oči souchotináře; vyvalený, modrý pohled
nějakého velkého vousatého chlapečka; kulaté a lesklé brýle examinujícího
profesora; ježčí zarostlá očička mrkající z ohromného chundele šedivých
chlupů; oči pátravé, nepřátelské, zapadlé, dětinské, svaté i podlé. Prokop
těkal pohledem po natřískaných lavicích a ucukl, jako by se spálil: potkal se
s pohledem rozcuchané dívky; prohnula se, jako by klesala do peřin, gestem
vlnivým a jednoznačným. Utkvěl na divné holé hlavě, pod níž visel úzký
kabátek; čertví je-li tomu tvoru dvacet let nebo padesát; ale než to rozřešil,
svraskla se celá hlava širokým, nadšeným a poctěným úsměvem. Jeden pohled ho
dráždil neodbytně; hledal jej mezi všemi, ale nenacházel ho.
Delegát Peters skončil koktaje a zmizel v lavici celý rudý. Všechny oči
dolehly na Prokopa napjatým a nutkavým očekáváním; stařík Mazaud něco formálně
odbreptal a naklonil se k Daimonovi. Bylo bezdeché ticho; a Prokop se zvedl
nevěda, co činí. „Slovo má kamarád Krakatit,“ ohlásil Mazaud mna si suché
ručičky.
Prokop se rozhlédl omámenýma očima: Cože mám dělat? Mluvit? Proč? Kdo jsou ti
lidé? – Zachytil laní oči souchotináře, přísný a zkoumavý lesk brýlí, mrkající
očka, oči zvědavé a cizí, lesklý a jihnoucí pohled krásné dívky; otevřela
hříšná, horká ústa samou pozorností; v první lavici holý a svraštělý človíček
visí na jeho rtech uchvácenýma očima. Usmál se na něj potěšen.
„Lidé,“ začal tiše a jako ve snách, „v noci včerejší… jsem zaplatil nesmírnou
cenu. Prožil jsem… a ztratil…“ Vší mocí se sebral. „Někdy zažiješ… bolest
takovou, že… že už není jen tvá. I otevřeš oči a vidíš. Zatměl se vesmír a
země tají dech útrapou. Svět musí být vykoupen. Neunesl bys své bolesti,
kdybys trpěl jen ty sám. Vy všichni jste prošli peklem, vy všichni –“
Rozhlédl se po světnici; vše mu splývalo v jakousi mdle zářící podmořskou
vegetaci. „Kde máte Krakatit?“ zeptal se najednou podrážděně. „Kam jste jej
dali?‘
Stařík Mazaud zvedl opatrně porcelánovou svátost a vložil mu ji do rukou. Byla
to táž krabice, kterou kdysi nechal ve svém laboratorním baráku u Hybšmonky.
Otevřel víko a hrábl prsty do zrnitého prášku, mnul jej, rozmílal, čichal k
němu, vložil si špetku na jazyk; poznal jeho svěravou, silnou hořkost a
okoušel ji s rozkoší. „To je dobře,“ vydechl a tiskl tu drahocennou věc v obou
dlaních, jako by si o ni ohříval zkřehlé ruce.
„Ty jsi to,“ bručel polohlasně; „já tě znám; ty jsi výbušný živel. Přijde tvůj
okamžik, a vydáš vše; tak je to dobře.“ Vzhlédl nejistě z podobočí: „Co byste
chtěli vědět? Já rozumím jenom dvěma věcem: hvězdám a chemii. Krásné jsou…
nesmírné rozlohy času, věčný pořádek a stálost, a božské počtářství vesmíru;
říkám vám, že… nic není krásnějšího. Ale co mně jsou platny zákony věčnosti?
Přijde tvůj okamžik, a vybuchneš; vydáš lásku, bolest, myšlenku, já nevím co;
tvé největší a nejsilnější je jenom okamžik. Ty, ty nejsi vřazen do
nekonečného řádu ani započítán do miliónů světelných let; a tedy… tedy ať to
tvé nic stojí za to! Vybuchni plamenem nejvyšším. Cítíš se sevřen? Tak tedy
roztrhni svůj crusher a rozmetej skálu. Udělej místo pro svůj jediný okamžik.
Tak je to dobře.“
Nechápal sám jasně, co mluví; ale unášelo ho temné puzení vyslovit něco, co mu
hned zase unikalo. „Já… dělám jen chemii. Znám hmotu a… rozumím si s ní; to je
všecko. Hmota se drobí vzduchem a vodou; štěpí se, kvasí, hnije, hoří, přijímá
kyslík nebo se rozpadá; ale nikdy, slyšíte, nikdy při tom nevydá vše, co v ní
je. A kdyby prošla celým koloběhem; kdyby se některý prášek země vtělil v
rostlinu a v živé maso a stal se myslící buničkou mozku Newtonova, a umřel s
ním a znovu se rozpadl, nevydal by všecko. Ale přinuťte jej… násilím, aby se
roztříštil a rozpoutal; hle, vybuchl v tisícině vteřiny; nyní, nyní teprve
vynaložil všechnu svou schopnost. A snad ani nespal; byl jenom spoután a dusil
se, zápasil potmě a čekal, až přijde jeho okamžik. Vydat vše! Je to jeho
právo. Já, já také musím vydat vše. Mám jenom zvětrávat a čekat… kvasit
nečistě… a drobit se, aniž bych kdy… rázem… vydal celého člověka? Raději… to
už raději v jediné vrcholné chvíli… a přese vše… Neboť já věřím, že je dobře
vydat všecko. Ať je to dobré nebo špatné. Všecko je ve mně srostlé: dobré a
zlé a nejvyšší. Kdo žije, dělá zlé i dobré, jako by se drobil. Dělal jsem to i
to; ale nyní… musím vydat to nejvyšší. To je vykoupení člověka. Není to v
ničem, co jsem udělal; je to zarostlé ve mně… jako v kameni. I musím se
roztrhnout… mocí… jako se roztrhne náboj; a nebudu se ptát, co přitom
roztříštím; ale bylo třeba… bylo mně třeba vydat to nejvyšší.“
Zápasil se slovy, namáhal se obsáhnout něco nevýslovného; ztrácel to každým
slovem, vraštil čelo a hledal v tváři naslouchajících, zda snad nepochytili
smysl toho, co nebyl s to jinak vyslovit. Našel zářivou sympatii v čistých
očích souchotináře a soustředěné úsilí ve vyjevených modrých očkách vousatého
obra tam vzadu; svrasklý človíček pil jeho slova s bezmeznou oddaností
věřícího a krásná dívka je přijímala, polo ležíc, milostnými záchvěvy těla.
Zato ostatní tváře na něho civěly cize, zvědavě nebo s rostoucí lhostejností.
Proč vlastně mluvím?
„Prožil jsem,“ pokračoval tápavě a poněkud již rozdrážděn, „prožil jsem tolik…
co člověk může prožít. Proč vám to říkám? Protože nemám dost na tom; protože…
doposud nejsem vykoupen; nebylo v tom to nejvyšší. Je to… zapadlé v člověku
jako ve hmotě síla. Hmotu musíš porušit, aby vydala svou sílu. Člověk se musí
rozpoutat, a porušit, a roztříštit, aby vydal svůj nejvyšší plamen. Aá, to by…
to by už bylo příliš, aby ani pak nenašel, že… že dosáhl… že… že…“
Zakoktal se, zamračil se, hodil krabici s Krakatitem na katedru a usedl.


XLIX.

Bylo chvíli rozpačité ticho.
„A to je všechno?“ ozval se ze středu lavic výsměšný hlas.
„To je všechno,“ zabručel Prokop znechucen.
„Není.“ To řekl Daimon a vstal. „Kamarád Krakatit předpokládal, že delegáti
mají dobrou vůli rozumět –“
„Oho!“ zahlučelo to ve středu.
„Ano. Delegát Mezierski už musí mít trpělivost, až domluvím. Kamarád Krakatit
nám obrazně řekl, že je třeba,“ a tu Daimonův hlas zněl opět skřekem ptačím,
„že je třeba zahájit revoluci bez ohledu na teorii etap; revoluci ničivou a
výbušnou, ve které vydá lidstvo to nejvyšší, co v něm je utajeno. Člověk se
musí roztříštit, aby vydal vše. Společnost se musí roztříštit, aby v sobě
našla nejvyšší dobro. Vy se tady léta hádáte o nejvyšší dobro lidstva. Kamarád
Krakatit nás poučil, že stačí uvést lidstvo v explozi, aby vyšlehlo daleko
výše, než jak by mu chtěly předpisovat vaše debaty; a neohlížet se na to, co
se přitom rozbije. Pravím, že kamarád Krakatit má pravdu.“
„Má, má, má!“ Najednou se strhl křik a potlesk. „Krakatit! Krakatit!“
„Ticho,“ překřikl je Daimon. „A jeho slova mají tím větší váhu, že jsou
podložena faktickou mocí tento výbuch provést. Kamarád Krakatit není muž slov,
nýbrž činu. Přišel, aby nám uložil přímou akci. A já vám říkám, že bude
strašlivější, než se kdo odvážil snít. A vypukne dnes, zítra, do týdne –“
Jeho slova zanikla v nepopsatelné vřavě. Vlna lidí se smýkla z lavic na pódium
a obklopila Prokopa. Objímali ho, tahali ho za ruce a křičeli „Krakatit!
Krakatit!“ Krásná dívka s vlasy rozpoutanými divě zápasila, aby se k němu
prodrala klubkem lidí; vržena jich tlakem přilnula k němu hrudí; chtěl ji
odstrčit, objala ho a něco horečně sykala cizím jazykem. Zatím na kraji pódia
muž s brýlemi pomalu a tiše vykládal do prázdných lavic, že teoreticky není
přípustno vyvozovat sociologické závěry z neústrojné přírody. „Krakatit,
Krakatit,“ hučel dav, nikdo neseděl, Mazaud třepal zvonkem jako popelář; a
najednou se na katedru vyšvihl černý mladý muž a vysoko nade všemi mával ve
vztyčené ruce krabicí s Krakatitem.
„Ticho,“ zařval, „a dolů! nebo vám to hodím pod nohy!“
Nastalo náhlé ticho; klubko se smeklo z pódia a couvalo. Nahoře zůstal jen
Mazaud se zvonkem v ruce, zmatený a bezradný, Daimon opřený o tabuli a Prokop,
na němž dosud visela ta temnovlasá menáda.
„Rosso,“ ozvaly se hlasy. „Srazte ho! Rosso dolů!“
Mladý muž na katedře divoce těkal žhoucíma očima. „Nikdo se nehni! Mezierski
chce na mne střelit. Hodím,“ zaryčel a zatočil krabicí.
Dav couval mruče jako rozlícená šelma. Dva tři lidé zvedli ruce, jiní
následovali; byla chvilka dusného mlčení.
„Jdi dolů,“ rozkřikl se stařík Mazaud. „Kdo ti dal slovo?“
„Hodím,“ hrozil Rosso napjatý jako luk.
„To je proti jednacímu řádu,“ rozčilil se Mazaud. „Já protestuju a… skládám
předsednictví.“ Mrštil zvonkem na zem a sestoupil z pódia.
„Bravo, Mazaud,“ ozval se ironický hlas.
„Tys tomu pomohl.“
„Ticho,“ křičel Rosso a shazoval si vlasy s čela. „Já mám slovo. Kamarád
Krakatit nám řekl: Přijde tvůj okamžik, a vybuchneš; udělej místo pro svůj
jediný okamžik. – Dobrá, já jsem si vzal jeho slova k srdci.“
„To nebylo tak myšleno!“
„Ať žije Krakatit!“
Někdo začal hvízdat.
Daimon chopil Prokopa za loket a táhl ho k jakýmsi dvířkám za tabulí.
„Můžete hvízdat,“ pokračoval Rosso výsměšně. „Nikdo z vás nehvízdal, když se
před vás postavil tady ten cizí pán a… dělal místo pro svůj okamžik. Proč by
to nezkusil někdo jiný?“
„To je pravda,“ ozval se pokojný hlas.
Krásné děvče se postavilo před Prokopa, aby ho krylo svým tělem. Chtěl ji
odstrčit.
„Není to pravda,“ křičela s očima planoucíma. „On… on je…“
„Buď tiše,“ sykl Daimon.
„Poroučet dovede každý,“ mluvil Rosso zimničně. „Pokud mám tohle v ruce,
poroučím já. Mně je jedno, pojdu-li. Nikdo nesmí ven odtud! Galeasso, hlídej
dveře! Tak, teď si promluvíme.“
„Ano, teď si promluvíme,“ ozval se Daimon ostře.
Rosso se bleskem obrátil k němu; ale v tom okamžiku se vyřítil z lavic
modrooký obr s hlavou skloněnou jako beran, a dříve než se Rosso otočil,
popadl ho za nohy a podtrhl mu je; hlavou dolů letěl Rosso z katedry. V
úděsném tichu bouchne a zapraská hlava na prknech, a z pódia se kutálí víčko
porcelánové krabice dolů a pod škamna.
Prokop se hnal k bezduchému tělu; na Rossových prsou, na tváři, po zemi, v
kalužích krve byl rozsypán bílý prášek Krakatitu. Daimon jej zadržel; a tu již
se rozpoutal křik a několik lidí běželo na pódium. „Nešlapat na Krakatit,
vybuchne to,“ kázal nějaký roztřesklý hlas, ale už se vrhali na zem a sbírali
bílý prášek do krabiček od sirek, rvali se, váleli se v klubku na zemi.
„Zamkněte dveře,“ zaryčel kdosi. Světlo zhaslo. V tu chvíli rozkopl Daimon
dvířka za tabulí a vytáhl Prokopa do tmy.
Posvítil si kapesní baterkou. Byl to kumbálek bez oken, stoly nakladené na
sobě, pivní tácky, nějaké zatuchlé šatstvo. Rychle táhl Prokopa dál: kyselá
černá díra chodby, černé a úzké schody dolů. Na schodech je dohonila
rozcuchaná dívka. „Jdu s vámi,“ šeptala a zaryla prsty do Prokopovy paže.
Daimon vyrazil na dvůr kmitaje před sebou kruhem světla; byla propastná tma.
Vytrhl vrátka a pádil na silnici; a než Prokop doběhl k vozu, pokoušeje se
střásti dívku, hrčel motor a Daimon skočil k volantu. „Rychle!“ Prokop se vrhl
do vozu a děvče za ním; vůz sebou trhl a letěl do tmy. Byla ledová zima; děvče
se třáslo v tenkých šatech, i zabalil ji Prokop do kožišiny a sám se vtiskl do
druhého kouta. Vůz uháněl špatnou měkkou cestou, zmítal se z boku na bok,
vysazoval a opět rachotivě nabíral rychlosti. Prokop mrzl a uhýbal, kdykoliv
jej náraz vozu hodil k schoulené dívce. Svezla se k němu. „Je ti zima, viď?“
šeptala, rozevřela kožišinu a halila ho do ní táhnouc ho k sobě. „Ohřej se,“
vydechla s šimravým smíchem a přimkla se k němu celým tělem; byla horká a
kyprá, jako by nahá byla. Její rozpoutané vlasy vydechovaly pach hořký a
divoký, dráždily ho na tvářích a oslepovaly mu oči. Mluvila k němu zblizoučka
cizím jazykem, opakovala to ještě tišeji, ještě tišeji, brala jeho boltec mezi
jemně jektající zuby; a náhle mu leží na prsou a vniká do jeho úst neřestným,
zkušeným, vláhyplným polibkem. Hrubě ji odstrčil; vztyčila se žasnouc, uraženě
odsedla a pohybem ramen smekla se sebe kožišinu; dulo mrazivě, i zvedl kožich
a položil jí jej znovu na ramena. Hodila sebou vztekle, vzdorovitě strhla
kožišinu a nechala ji válet na dně vozu. „Jak chcete,“ zabručel Prokop a
odvrátil se.
Vůz vyjel opět na tvrdou cestu a rozehnal se vyjící rychlostí. Z Daimona
nebylo vidět než záda zježená kozími chlupy. Prokop se zalykal studeným
větrem; ohlédl se po dívce, otočila si vlasy kolem krku a zajíkala se zimou ve
svých lehkých šatečkách. Bylo mu jí líto; sebral kožich a hodil jej na ni;
odstrkovala jej v rozlíceném vzdoru, a tu ji celou omotal kožišinou s hlavou a
se vším všudy jako balík a sevřel ji pažema: „Ani se nehnout!“
„Co, už zas vyvádí?“ hodil Daimon pokojně od volantu. „Nu tak ji…“
Prokop dělal, jako by přeslechl jeho cynismus; ale spoutaný balík v jeho
pažích se počal tiše chichtat.
„Je to hodná holka,“ pokračoval Daimon lhostejně. „Tvůj tatík byl spisovatel,
viď?“ Balík pokývl; a Daimon řekl Prokopovi jméno tak známé, tak osvícené a
čisté, že Prokop ustrnul a mimoděk uvolnil své drsné sevření. Balík sebou
zavrtěl a vyhoupl se mu na klín; zpod kožišiny vyčouhly krásné, hříšné nohy a
dětsky se bimbaly ve vzduchu. Přetáhl přes ně kožich, aby nemrzla; považovala
to nejspíš za hru, dusila se tichým smíchem a kopala vyhazujíc nohama. Sevřel
ji co nejníže mohl; tu zas nahoře vyklouzla plná děví ruka a vjela mu do
obličeje v divé a milostné hře, rvala ho za vlasy, dráždila na krku, dobývala
se prsty do jeho sevřených úst. Nechal ji posléze činit; dotkla se jeho čela,
našla je přísně svraštěné a utrhla, jako by se spálila; teď je to bojácná
dětská pracička, která neví, co smí; kradmo se blíží k jeho tváři, dotkne se
jí, ucukne, znovu se dotkne, pohladí a lehce, bázlivě se položí na hrubou líc.
V kožichu to hluboce vzdychlo a znehybnělo.
Auto se protáčí spícím městečkem a klesá do širého kraje. „Tak co,“ obrací se
Daimon, „co říkáte kamarádům?“
„Tiše,“ šeptá nehybný Prokop „usnula.“


L.

Vůz zastavil v černém, lesnatém údolí. Prokop rozeznal potmě těžné věže a
haldy. „Tak, tady jsme,“ zabručel Daimon. „To je můj rudný důl a hamr; nestojí
to za nic. Nu, vystupte!“
„Mám ji tady nechat?“ ptal se Prokop tiše.
„Koho? Aha, vaši krasavici. Probuďte ji, zůstaneme tady.“
Prokop opatrně vystoupil nesa ji v náručí. „Kam ji mám položit?“
Daimon odemykal ponurý dům. „Co? Počkejte, já tu mám několik pokojů. Můžete ji
položit… já vás tam dovedu.“
Rozsvítil a vedl ho studenými kancelářskými chodbami; konečně vešel do jedněch
dveří a otočil kontakt. Byl to ošklivý nevyvětraný pokoj se zválenou postelí a
spuštěnou žaluzií. „Aha,“ bručel Daimon, „nocoval tu asi… jeden známý. Moc
pěkné to tu není, že? Nu, jako u mládence. Položte ji sem na postel.“
Prokop opatrně složil tiše oddychující balík. Daimon přecházel a mnul si ruce.
„Půjdeme teď do naší stanice. Je nahoře, na kopci, deset minut odtud. Nebo
chcete zůstat tady?“ Přistoupil k spící dívce, rozhodil cíp kožichu a odkryl
její nohy až nad kolena. „Je krásná, viďte? Škoda že jsem tak starý.“
Prokop se zamračil a zahalil jí nohy. „Ukažte mi vaši stanici,“ řekl suše.
Ústy Daimonovými trhl úsměšek. „Pojďte.“
Vedl ho dvorem. Ve strojovně se svítí, mašiny supají, po dvoře se potlouká
topič s rukávy vyhrnutými a kouří dýmčičku. Nahoru do stráně vede lanová dráha
na rudné vozíky a její konstrukce se rýsuje mrtvě jako ještěří žebra. „Musel
jsem zavřít tři jámy,“ vykládá Daimon. „Nevyplácí se to. Už bych to dávno
prodal, nebýt stanice. Pojďte tudy.“ Pustil se po příkré pěšině lesem a do
kopce; Prokop jej sledoval jen po zvuku; byla černá tma a časem skanula ze
smrků těžká krůpěj. Daimon se zastavil a s námahou oddechoval. „Jsem stár,“
řekl, „už nemám dechu jako dřív. Musím víc a více spoléhat na lidi… Dnes nikdo
na stanici není; kamarád telegrafista zůstal tam, s nimi… To je jedno;
pojďte!“
Temeno kopce bylo rozryté jako bojiště: opuštěné těžné věže, drátěná lana,
ohromné pusté haldy; a na největší haldě nahoře dřevěný baráček s anténami.
„To je… stanice,“ supěl udýchaný Daimon. „Stojí… na čtyřiceti tisících tunách
magnetitu. Přirozený kondenzátor, rozumíte? Celý kopec… je ohromná síť drátů.
Někdy vám to vyložím podrobně. Pomozte mi nahoru.“ Vydrápali se po sypké
haldě; těžký štěrk se jim s rachotem svážel pod nohama; ale konečně tady, tady
je stanice –
Prokop ustrnul nevěře svým očím: vždyť je to jeho laboratorní barák, tam doma,
v polích nad Hybšmonkou! tady ty nenatřené dvéře, pár světlejších prken od
poslední správky, suky podobné očím – Jako vyjevený hmátl na veřeje: ovšem,
tuhle je ten rezavý ohnutý hřebík, který sám kdysi zatloukl! „Kde se to tu
vzalo?“ vyhrkl bezdeše.
„Co?“
„Ten barák.“
„Ten už tu stojí léta,“ řekl Daimon lhostejně. „Co na něm máte?“
„Nic.“ Prokop oběhl celý domek hmataje po stěnách a oknech. Ano, tady je ta
štěrbina, prasklé dřevo, vyražená tabulka v okně; vypadlý suk, pravdaže, a z
nitra zalepený papírem. Třesoucí se rukou přejížděl známé ubohé podrobnosti;
všecko je, jak to bylo, všecko…
„Nu tak,“ ozval se Daimon, „už jste si to prohlédl? Otevřte, vy máte klíč.“
Prokop jel rukou do kapsy. Nu ovšem, měl s sebou klíč od své staré laboratoře…
tam doma; vstrčil jej do visacího zámku, odemkl a vešel dovnitř; a – jako tam
doma – hmátl mechanicky vlevo a otočil kontaktem, který měl místo knoflíku
hřebík – jako tam doma. Daimon vešel za ním. Bože, tady je můj kavalec dosud
neustlaný; mé umyvadlo, džbán s okrajem potlučeným, houba, ručník, vše –
Otočil se do rohu; a to jsou ta stará železná kamínka s rourou spravovanou
drátem, bednička s uhelným mourem, a tam je rozbitá lenoška s nohama
pokleslýma, a čouhá z ní koudel a stočený drát; tady je ten cvoček v podlaze,
a tu je to propálené prkno, a skříň, skříň na šaty – Otevřel ji; klátily se
tam nějaké zvadlé kalhoty.
„Skvělé to tu není,“ poznamenal Daimon. „Náš telegrafista je takový – nu,
podivín. Co říkáte aparátům?“
Prokop se obrátil ke stolu jako v snách. Ne, to tu nebylo, nenene, to sem
nepatří: místo nářadí chemikova je na jednom konci pultu tuctová lodní
radiostanice s položeným sluchátkem, přijímací aparát, kondenzátory,
variometr, regulátor, pod stolem obyčejný transformační agregát; a na druhém
konci –
„Tamto je normální stanice,“ vysvětloval Daimon, „na obyčejné hovory. To druhé
je naše extinkční stanice. Tou posíláme ty antivlny, protiproudy, umělé
magnetické bouře nebo jak to chcete jmenovat. To je celé naše tajemství.
Vyznáte se v tom?“
„Ne.“ Prokop zběžně přehlédl aparáty zcela nepodobné všemu, co znal. Mělo to
spoustu odporů, jakousi drátěnou mřížku, cosi podobného katodové trubici,
nějaké izolované bubny či co a podivný koherer, relé a tastr s kontakty;
nevěděl, co to vůbec je. Nechal aparátu a koukal na strop, je-li na něm také
ta divná kresba dřeva, která mu tam doma vždycky připomínala hlavu starce.
Ano, je je je tam. A tamhle je to zrcátko s uraženým rohem –
„Co říkáte aparátu?“ ptal se Daimon.
„Je – eh – to je první konstrukce, že? Je to ještě příliš složité.“ Padl očima
na fotografii, jež byla opřena o jakousi indukční cívku. Vzal ji do rukou;
byla to opojně krásná dívčí hlava. „Kdo je to?“ ptal se chraptivě.
Daimon mu nahlédl přes rameno. „Copak ji nepoznáváte? To je ta vaše krasavice,
co jste si ji sem přivezl v náručí. Skvostná holka, že?“
„Jak se sem dostala?“
Daimon se ušklíbl. „Nu, asi ji zbožňuje náš telegrafista. Nechtěl byste
zapnout tamten veliký kontakt? Ten pákový. – Je to ten scvrklý človíček,
nevšiml jste si ho? Seděl v první lavici.“
Prokop hodil fotografii na stůl a zapnul kontakt. Po drátěné mřížce přeběhla
modrá jiskra. Daimon si pohrál prsty na tastru; tu začal celý aparát
světélkovat krátkými modrými zášlehy. „Tak,“ vydechl Daimon spokojeně a
zadíval se bez hnutí do sršících jisker.
Prokop popadl fotografii horečnýma rukama. Nu ovšem, rozumí se, to je to děvče
dole; o tom nemůže býti pochyby. Ale kdyby… kdyby snad měla závoj, a
kožišinku, zrosenou kožišinku až po ústa… a rukavičky – Prokop zaťal zuby. To
není možno, že by jí byla tak podobna! Nachmuřil oči stíhaje unikající vidinu:
zas viděl dívku v závoji, tiskne k prsoum zapečetěnou obálku a teď, teď k němu
obrací čistý a zoufalý pohled –
Bez sebe rozechvěním srovnával obrázek s uniklou podobou. Bože na nebi, jak
vlastně vypadala? Vždyť já to nevím, lekl se; vím jen, že byla zastřená a
krásná. Krásná byla a zastřená, a nic víc, nic víc jsem neviděl. A tohle, ten
obrázek tady, veliké oči a ústa vážná a jemná, to že je ta – ta – ta spící
dole? Ta má přec ústa rozevřená, hříšná a rozevřená ústa a vlasy rozpoutané, a
nedívá se tak – nedívá se tak – Zrosený závoj mu zastřel oči. Ne, to je
nesmysl; toto vůbec není to děvče dole, a není jí ani podobna. Toto je tvář té
zastřené, jež přišla v hoři a úzkosti; její čelo je klidné a oči jsou
zastíněny bolestí; a ke rtům se jí lepí závoj, hustý závoj s rosičkou dechu –
Proč tehdy jej nezvedla, abych ji poznal!
„Pojďte, něco vám ukážu,“ ozval se Daimon a táhl Prokopa ven. Stáli na vrcholu
haldy; pod jejich nohama temná a spící země do nedozírna. „Dívejte se tamhle,“
řekl Daimon a ukazoval rukou k obzoru. „Nevidíte nic?“
„Nic. Ne, je tam světélko. Slabá záře.“
„Víte, co to je?“
Tu zahučelo slabě, jako by zaryl vítr v noční tišině. „Hotovo,“ děl Daimon
slavnostně a smekl čepici. „Good night, kamarádi.“
Prokop se k němu tázavě obrátil.
„Nerozumíte?“ povídal Daimon. „Teď teprve k nám doletěl výbuch. Padesát
kilometrů vzdušné čáry. Přesně dvě a půl minuty.“
„Jaký výbuch?“
„Krakatit. Ti pitomci si to cpali do sirkových škatulek. Myslím, že už budeme
mít od nich pokoj. Svoláme nový sjezd – bude nový výbor –“
„Vy – jste je –“
Daimon přikývl. „S nimi se nedalo pracovat. Jistě že se hádali do poslední
chvíle o taktiku. Nejspíš tam hoří.“
Na obzoru bylo vidět jen slaboučkou červenou záři.
„Zůstal tam i vynálezce naší stanice. Zůstali tam všichni. Teď tedy to vezmete
do ruky sám – Hleďte, poslouchejte, jak je ticho. A přece odtud, tady z těch
drátů, šlehá do prostoru němá a přesná kanonáda. Teď jsme zastavili všechny
bezdrátové spoje, a telegrafistům to práská do uší, krach, krach! Ať se
vztekají. Zatím se pan Tomeš někde v Grottup pachtí dodělat Krakatit – Nenajde
to nikdy. A kdyby, kdyby! v tom okamžiku, jak by se mu to pod rukou sloučilo,
byl by konec – Tak jen pracuj, staničko, jiskři potichu a bombarduj celý
vesmír; nikdo, nikdo kromě vás nebude pánem Krakatitu. Teď jste jen vy, vy
sám, vy jediný –“ Položil mu ruku na rameno a ukázal mlčky kolem dokola: celý
svět. Byla tma bezhvězdná a pustá; jen na obzoru žířila nízká ohnivá záplava.
„Ah, jsem unaven,“ zívl Daimon. „Byl to slušný den. Pojďme dolů.“


LI.

Daimon spěchal, aby už byl doma. „Kde je vlastně Grottup?“ zeptal se Prokop
zčistajasna, když už byli dole.
„Pojďte,“ děl Daimon, „ukážu vám to.“ Dovedl ho do tovární kanceláře a k
nástěnné mapě. „Tady,“ ukázal ohromným nehtem na mapě podškrtávaje malé
kolečko. „Nechcete pít? To vás zahřeje.“ Naléval sobě i Prokopovi do skleniček
něco černého jako smůla. „Na zdraví.“ Prokop do sebe obrátil kalíšek a zajíkl
se; bylo to jako rozžhavené železo a hořké jako chinin; hlava se mu zatočila
nesmírnou závratí. „Už nechcete?“ vycenil Daimon žluté zuby. „Škoda. Nechcete
nechat čekat svou krasotinku, že?“ Pil sklenku po sklence; oči mu zeleně
blýskaly, chtěl žvanit, ale jazyk mu tuhl. „Poslyšte, vy jste chlapík,“
prohlašoval. „Zítra se do toho dejte. Starý Daimon vám udělá všechno, nač si
vzpomenete.“ Zvedl se toporně a klaněl se mu až po pás. „Tak je to v pořádku.
A teď – poč-počkejte –“ Počaly se mu plést všechny jazyky světa; pokud Prokop
rozuměl, byly to nejhrubší oplzlosti; nakonec bručel nesmyslnou písničku,
trhal sebou jako v padoucnici a ztrácel vědomí; na rtech mu vystoupila žlutá
pěna.
„Hej, co je vám?“ křičel Prokop a zatřásl jím.
Otevřel těžce a blbě skelné oči. „Co… co je?“ blábolil, trochu se zvedl a
otřásl se. „Aha, já – já jsem – To nic není.“ Promnul si čelo a křečovitě
zíval. „A-ano, já vás dovedu do vašeho pokoje, že?“ Byl ošklivě zsinalý a celý
jeho tatarský obličej váčkovitě splaskl; vrávoral nejistě, jako by mu ztuhly
údy. „Tak pojďte.“
Šel rovnou do pokoje, kde nechali spící dívku. „Aa,“ křikl ve dveřích,
„krasavice se probudila. Račte dál.“
Klečela u kamen; patrně právě zatopila, a dívala se do praskajícího plamene.
„Vida, jak to tu poklidila,“ bručel Daimon uznale. Skutečně, bylo vyvětráno a
trapný nelad pokoje kupodivu zmizel; bylo tu nenáročně a příjemně jako v
pokojné domácnosti.
„Hleďme, co dovedeš,“ divil se Daimon. „Holka, ty bys už měla zakotvit.“
Vstala a neobyčejně se začervenala i zmátla. „Nu, jen se neplaš,“ cenil se
Daimon. „Tedy ten kamarád se ti líbí, viď?“
„Líbí,“ řekla prostě a šla zavřít okno a spustit žaluzii.
Kamna teple zadýchala do světlého pokoje. „Děti, máte to tu pěkné,“ liboval si
Daimon a nahříval si ruce u kamen. „Hned bych tu zůstal.“
„Jen si jdi,“ vyhrkla rychle.
„Sejčas, holubičko,“ zubil se Daimon. „Mně… mně je teskno bez lidí. Koukej,
tvůj přítel je jako zařezaný. Počkej, já mu domluvím.“
Rozzlobila se prudce. „Nic mu nedomluvíš! Ať je, jaký chce!“
Zvedl chlupaté obočí přeháněje úžas. „Copak? copak? snad jsi se do něho ne-
nezami –“
„Co je ti po tom?“ přerušila ho blýskajíc očima. „Kdo tě tu potřebuje?“
Řehtal se tiše opřen o kamna. „Kdybys věděla, jak ti to sluší! Holka, holka, i
na tebe to jednou přišlo doopravdy? Ukaž se!“ Chtěl ji vzít za bradu;
ustoupila blednouc hněvem a ukázala zuby.
„Cože? I kousat chceš? S kýmpak jsi včera zas byla, že jsi tak – Aha, já už
vím; Rosso, viď?“
„To není pravda,“ křikla se slzami v hlase.
„Nechte ji,“ ozval se Prokop příkře.
„Nunu, vždyť o nic nejde,“ bručel Daimon. „Tak abych vám nepřekážel, že?
Dobrou noc, děti.“ Couval a tlačil se ke zdi; a než Prokop vzhlédl, byl pryč.
Prokop si přitáhl židli k hučícím kamnům a zadíval se do plamene; ani se po ní
neohlédl. Slyšel ji, jak váhavě, po špičkách přechází po pokoji, zamyká a něco
rovná; neví už co, stojí a mlčí – Divná je moc plamene a plynoucích vod;
člověk se zahledí, omámí, zastaví; nemyslí už, neví a nevzpomíná, ale děje se
v něm všechno, co kdy žil, co kdy žil, bez tvaru a bez času.
Klapl jeden pohozený střevíček a druhý; asi se zouvá. Jdi spat, děvče; až
usneš, podívám se, komu jsi podobna. Tichounce přešla a zastavila se; zas něco
přerovnává, bůhví proč to tu chce mít tak pěkné a čisté. A najednou před ním
klečí na kolenou a vztahuje sličné ruce k jeho noze. „Zuju ti boty, nechceš?“
povídá tiše.
Vzal její hlavu do dlaní a obrátil ji k sobě. Krásná, poddajná a podivně
vážná. „Znala jsi Tomše?“ ptal se chraptivě.
Přemýšlela a zavrtěla hlavou.
„Nelži! Ty jsi – ty jsi – Máš vdanou sestru?“
„Nemám.“ Vydrala se mu prudce z dlaní. „Proč bych ti lhala? Všechno ti řeknu
naschvál abys věděl – na-schvál – Já já jsem zkažená holka.“ Zaryla se mu
tváří do kolen. „Všichni mne všich-ni abys to věděl –“
„I Daimon?“
Neodpověděla, jenom se otřásla. „Mů-můžeš mne kopnout já jsem óó nnnenesahej
na mne – já jsem kdy-bys věděl…“ Zrovna ztuhla.
„Nech toho,“ křikl zmučen a násilím zvedl jí hlavu. Její oči široce zely
úzkostí a zoufáním. Pustil ji a zaúpěl. Byla to taková podoba, že se zalykal
úděsem. „Mlč, mlč aspoň,“ mručel s hrdlem sešněrovaným.
Znovu se mu zaryla tváří do klína. „Nech mne já musím vvvšechno… Já já začala
když mně bylo tři-třináct…“ Zacpal jí dlaní ústa; kousala ho do ruky a mumlala
svou děsnou zpověď mezi jeho prsty. „Buď tiše,“ křičel, ale dralo se to z ní,
jektala zuby a třásla se, mluvila, koktala – Jakžtakž ji umlčel. „Óó,“
sténala, „kdybys… věděl… co… co lidé… co dě-lají! A každý, každý byl ke mně
tak hrubý… Jako bych ani… ani zvíře, ani kámen nebyla!“
„Přestaň,“ vydechl bez sebe a nevěda co činit hladil ji třesoucími se pahýly
prstů po hlavě. Vzdychla uklidněně a znehybněla; cítil její palčivý dech a tep
jejího hrdla.
Začala se tiše chichtat. „Ty sis myslel, že spím… tam ve voze. Já jsem
nespala, já já jsem tak jenom naschvál dělala… a čekala jsem, že začneš… jako
jiní. Vždyťs věděl, co jsem a jaká jsem… A… ty ses jen mračil a držels mne,
jako bych byla malá holčička, jako bych… nějaká… svátost byla…“ Uprostřed
smíchu jí vytryskly slzy. „Já já byla najednou já nevím proč tak ráda jako
nikdy jako nikdy – a pyšná – a styděla jsem se hrozně, ale… přitom mně bylo
tak krásně –“ Štkajícími ústy mu líbala kolena. „Vy… vy jste mne ani
neprobudil… a položil… jako svátost… a nohy zakryl, a nic neřekl –“ Rozplakala
se nadobro. „Já já vám budu sloužit, nechte mne nechte mne… já vám zuju boty…
Prosím vás, prosím vás nezlobte se, že jsem dělala, jako bych spala! Prosím
vás –“
Chtěl jí zvednout hlavu; líbala mu ruce. „Proboha, neplačte!“ vyhrkl.
„Kdo?“ protáhla udivena a přestala plakat. „Proč mně vykáte?“ Obracel jí tvář
nahoru; bránila se vší silou a zavrtávala se mu do kolen. „Ne, ne,“ drkotala s
hrůzou a smíchem. „Já jsem uřvaná. Já já bych se vám… nelíbila,“ vydechla tiše
a schovávala uplakanou tvář. „Když jste tak… dlouho… nešel! Já vám budu
sloužit a psát dopisy… já já se naučím psát na stroji, já umím pět řečí –
nevyženete mne? Když jste tak dlouho nešel, myslela jsem co co všechno bych
udělala… a on mi to zkazil on mluvil jako bych… jako bych byla… A není to
pravda… já já už jsem řekla všechno; já budu… já udělám co řeknete… já chci
být hodná –“
„Vstaňte, prosím vás!“
Posadila se na paty, složila ruce v klín a hleděla na něho jako u vytržení.
Nyní… nebyla už podobna oné v závoji; vzpomněl si na štkající Anči. „Už
neplačte,“ zabručel měkce a nejistě.
„Vy jste krásný,“ vydechla s obdivem. Začervenal se a mručel nevěda co.
„Jděte… spat,“ zajíkal se a pohladil ji po palčivé líci.
„Neošklivím se vám?“ šeptala zrůžovělá.
„Ne, naprosto ne.“ Nehnula se a pohlížela na něho úzkostiplnýma očima; i
sklonil se k ní a políbil ji; zarděla se a vrátila mu to zmateně a neobratně,
jako by líbala poprvé. „Jdi spat, jdi,“ zamumlal rozpačitě, „já ještě… musím…
něco rozvážit.“
Vstala poslušně a počala se tiše svlékat. Usedl do kouta, aby jí nepřekážel.
Odkládala šaty beze studu, ale také bez nejmenší frivolnosti, prostě a
samozřejmě jako žena ve své rodině; nespěchajíc rozepíná knoflíčky a rozvazuje
tkanice, tichounce skládá prádlo, smeká zvolna punčochy se silných a
dokonalých nohou; zamyslí se, hledí k zemi a hraje si po dětsku dlouhými,
bezúhonnými prsty chodidel; pohlédne na Prokopa, usměje se ruměnou radostí a
šeptá: „Já jsem tiše.“ Prokop ve svém koutě trne sotva dýchaje: vždyť je to
opět ona, dívka v závoji; toto silné, vyspělé a překrásné tělo je její; takto
vážně a krásně odkládá šat po šatu, tak jí splývají vlasy po pokojných
ramenou, tak, právě tak si hladí, zamyšlena a schoulena, plné a matné paže, a
takto, takto – Zavřel oči s tlukoucím srdcem. Zda jsi ji kdysi nevídal,
svíraje oči v nejpustší samotě, jak stojí pod tichou lampou rodiny, obrací k
tobě tvář a něco praví, co jsi neslyšel? Zda jsi tehdy, mačkaje si ruce mezi
koleny, nezahlédl pod víčky semknutými pohyb její ruky, pohyb prostý a sličný,
v němž byla všechna mírná a mlčelivá radost domova? Jednou se ti zjevila,
stála k tobě zády s hlavou nad něčím skloněnou; a jindy jsi ji viděl čtoucí
pod večerní lampou. Je toto snad jen pokračování, a zmizelo by to, kdybych
otevřel oči, a nezbylo by nic než samota?
Otevřel oči. Dívka ležela v posteli, přikryta až po bradu, a upírala na něho
oči v strašně pokorné lásce. Přistoupil k ní, sklonil se nad její tvář,
studoval její rysy s prudkou a netrpělivou pozorností. Vzhlédla tázavě a
dělala mu místo po svém boku. „Nene,“ zamručel a políbil ji lehce na čelo.
„Jen spi.“ Zavřela poslušně oči a ani nedýchala.
Vrátil se po špičkách do svého kouta. Ne, není jí podobna, ujišťoval se. Zdálo
se mu, že na něho hledí zpod přivřených víček; mučilo ho to, nemohl ani
myslet; mračil se, odvrátil hlavu, ale konečně vyskočil a po špičkách se šel
na ni podívat. Měla oči zavřené, ani nedýchala; byla sličná a oddaná. „Spi,“
zašeptal. Pokývla maličko hlavou. Zhasil a hmataje rukama se vrátil po
špičkách do svého kouta u okna.
Po předlouhé, přeteskné době se kradl ke dveřím jako zloděj. Neprobudí se?
Váhal s rukou na klice, s bouchajícím srdcem otevřel a vykradl se na dvůr.
Je dosud noc. Prokop se rozhlédl mezi haldami a přelezl plot. Dopadl na zem,
očistil se a hledal silnici.
Je stěží vidět na cestu. Prokop se rozhlíží a chvěje se chladem. Kam, kam
vlastně? Do Balttinu?
Šel několik kroků a zastavil se; stojí a kouká do země. Tedy do Balttinu?
Škytl hrubým, bezslzným pláčem a obrátil se na patě.
Do Grottup!


LII.

Divně se točí dráhy světa. Kdybys sčetl všechny své kroky a cesty, jakou
složitou podobu by to nakreslilo? Neboť svými kroky rýsuje každý svou mapu
země.
Byl večer, když stál Prokop před mřížovým plotem grottupských závodů. Je to
rozsáhlé barákové pole, ozářené mlhovými koulemi obloukových lamp; ještě svítí
jedno nebo dvě okna; Prokop tiskne hlavu mezi mřížové pruty a volá: Haló!
Přiblížil se vrátný nebo hlídač. „Co chcete? Dovnitř se nesmí.“
„Prosím vás, je u vás ještě pan inženýr Tomeš?“
„Co s ním chcete?“
„Musím s ním mluvit.“
„… Pan Tomeš je ještě v laboratoři. Nemůžete s ním mluvit.“
„Řekněte mu… řekněte mu, že na něj čeká jeho přítel Prokop… že mu má něco
dát.“
„Jděte dál od té mříže,“ bručel člověk a někoho zavolal.
Po čtvrthodině běžel kdosi v dlouhém bílém plášti k mříži.
„To jsi ty, Tomši?“ volal Prokop polohlasně.
„Ne, já jsem laborant. Pan inženýr nemůže přijít. Pan inženýr má důležitou
práci. Co si račte přát?“
„Musím s ním nutně mluvit.“
Laborant, otylý a čilý človíček, pokrčil rameny. „Prosím, to nepůjde. Pan
Tomeš dnes nemůže ani na vteřinu –“
„Děláte Krakatit?“
Laborant nedůvěřivě zafrkal. „Co vám je po tom?“
„Musím ho… před něčím varovat. Musím mu něco doručit.“
„Máte to dát mně. Já mu to donesu.“
„Ne, já… já to dám jenom jemu. Řekněte mu –“
„Tak si to prý máte nechat.“ Člověk v bílém plášti se otočil a odcházel.
„Počkejte,“ volal Prokop. „Dejte mu to. Vyřiďte mu… vyřiďte mu…“ Vylovil z
kapsy onu pomačkanou silnou obálku a podával ji skrze mříž. Laborant ji vzal
podezřivě mezi prsty, a Prokopovi bylo, jako by právě něco přetrhl. „Řekněte
mu, že… že tu čekám, že ho prosím, aby… aby sem přišel!“
„Já mu to dám,“ uryl laborant a odešel.
Prokop se posadil na patník. Z druhé strany plotu stál mlčelivý stín a hlídal
ho. Je syrová noc, holé větve se rozpínají do mlhy, je slizko a zebavě. Po
čtvrthodině někdo přichází k plotu; je to bledý nevyspalý chlapec s tváří jako
z tvarohu.
„Pan inženýr vzkazuje, že mnohokrát děkuje a že nemůže přijít a že nemáte
čekat,“ vyřizoval mechanicky.
„Počkejte,“ drtil Prokop netrpělivě. „Řekněte mu, že s ním musím mluvit; že…
že jde o jeho život. A že mu dám všechno, co chce, jen když… jen když mi pošle
jméno a adresu té dámy, co jsem mu od ní donesl tu obálku. Rozumíte mi?“
„Pan inženýr jenom vzkázal, že mnohokrát děkuje,“ opakoval chlapec ospale, „a
že nemáte čekat –“
„Ale tak u čerta,“ zaskřípal Prokop zuby, „vyřiďte mu, ať sem přijde, jinak že
se odtud nehnu. A ať nechá práce, nebo… nebo mu to vyletí do povětří,
rozumíte?“
„Prosím,“ řekl chlapec tupě.
„Ať… ať sem přijde! Ať mi dá tu adresu, jenom tu adresu, a… že mu pak nechám
všechno, rozuměl jste?“
„Prosím.“
„Tak už jděte, jděte rychle, u všech –“
Čekal v zimničné netrpělivosti. Není… není to lidský krok tam uvnitř? Zatanul
mu Daimon, jak nasupen, křivě fialovou hubu se dívá do modrých jisker své
stanice. A ten pitomec Tomeš nejde! Kutí tam něco, tam, co září to světlé
okno, a neví, neví, že je bombardován, že chvatnýma rukama zapaluje podkop sám
pod sebou a – Není to lidský krok? Nikdo nejde.
Hrubý kašel otřásá Prokopem. Všechno ti vydám, šílenče, přijdeš-li mi jenom
říci její jméno! Nechci už nic; nechci už nic než ji nalézt; všeho se vzdám,
jen mi nech to jediné! Utkvěl očima v prázdnu: nyní tu stojí zahalena závojem,
u nohou suché listí, bleďoučká a divně vážná v této zsinalé tmě; spíná na
prsou ruce, nemá už obálku, a hledí na něho hlubokýma, upřenýma očima; studené
mžení jí zrosilo závoj i kožišinku. „Byl jste ke mně nezapomenutelně laskav,“
praví tiše a zastřeně. Zvedl k ní ruce, zlomil ho lítý kašel. Óó, což nikdo
nepřijde? Vrhl se k mřížovému plotu, aby jej přelezl.
„Zůstaňte tam, nebo střelím,“ křikl stín za plotem. „Co tu chcete?“
Prokop pustil plot. „Prosím vás chraptěl zoufale, „– řekněte panu Tomšovi…
řekněte mu…“
„Řekněte si mu to sám,“ přerušil ho hlas nelogicky; „ale hleďte, ať už jste
pryč.“
Prokop usedl na patník. Snad Tomeš přijde, až mu to zase selže. Jistě, jistě
nenajde, jak se dělá Krakatit; pak přijde sám a zavolá mne… Seděl nahrben jako
prosebník. „Poslyšte,“ ozval se, „já vám dám… deset tisíc, když… když mne
pustíte dovnitř.“
„Já vás dám sebrat,“ zabručel hlas příkře a neodmluvně.
„Já – já –,“ koktal Prokop, „já chci jen vědět tu adresu, víte? já chci jen…
vědět… Já vám dám všechno, když mi to opatříte! Vy… vy jste ženat a máte děti,
ale já… já jsem sám… a chci jenom nalézt…“
„Ticho buďte,“ osopil se hlas. „Jste opilý.“
Prokop umlknul a komihal trupem na patníku. Musím čekat, přemýšlel tupě. Proč
nikdo nejde? Všechno mu dám, i Krakatit, i všechno ostatní, jen když… „Byl
jste ke mně nezapomenutelně laskav.“ Ne, bůh chraň: já jsem člověk zlý; ale
vy, vy jste ve mně vzbudila vášeň laskavosti; všechno na světě bych udělal,
když jste na mne pohlédla; vidíte, proto jsem tady. To nejkrásnější na vás je,
že máte nade mnou moc, abych vám sloužil; proto, slyšíte, proto vás musím
milovat!
„Co pořád máte?“ láteřil hlas za plotem. „Budete tiše nebo ne?“
Prokop vstal: „Prosím vás, prosím vás, řekněte mu –“
„Já na vás pošlu psa!“
K plotu se loudavě blížila bílá postava s hořícím uhlíkem cigarety. „To jsi
ty, Tomši?“ zavolal Prokop.
„Ne. Vy jste tu ještě?“ Byl to laborant. „Člověče, vy jste blázen.“
„Prosím vás, přijde sem Tomeš?“
„Ani ho nenapadne,“ povídal laborant opovržlivě. „Nepotřebuje vás. Za čtvrt
hodiny to máme hotovo, a pak, gloria victoria! pak se napiju.“
„Prosím vás, řekněte mu, ať… ať mně dá jen tu adresu!“
„To už vyřizoval kluk,“ vycedil laborant. „Pan inženýr řekl, abyste mu vlezl
na záda. Bude se vytrhovat z práce, ne? Teď, když je v nejlepším. Už to
vlastně máme, a teď jenom – a je to.“
Prokop vykřikl úděsem: „Běžte mu říci – rychle – ať nezapíná vysokou
frekvenci! Ať to zastaví! Nebo – nebo se stane – Běžte honem! On neví – on –
on neví, že Daimon – Proboha, zarazte ho!“
„Tja,“ vyprskl laborant v krátký smích. „Pan Tomeš ví, co má dělat; a vy –,“
tu vyletěl mříží hořící oharek, „dobrou noc!“
Prokop skočil k plotu.
„Ruce vzhůru,“ zařval uvnitř hlas a vzápětí pronikavě hvízdla hlídačská
píšťalka. Prokop se dal na útěk.
Ubíhal po silnici, skočil přes příkop a běžel po měkké louce; klopýtal
oranicí, upadl, sebral se a uháněl dál. Zastavil se s buchajícím srdcem. Kolem
dokola mlha a pustá pole; teď už mne nechytí. Naslouchal; bylo ticho, slyšel
jen svůj sípavý dech. Ale což – což když vyletí Grottup do povětří? Chytil se
za hlavu a běžel dál; sklouzl do hlubokého úvozu, vydrápal se nahoru a kulhaje
skákal přes zorané pole. Oživla bolest staré fraktury a v prsou ho palčivě
bodalo; nemohl dále, usedl na studenou mez a díval se na Grottup mlhavě zářící
svými obloukovými lampami. Vypadalo to jako světelný ostrov v nesmírných
temnotách.
Je trnoucí, zdušené ticho; a přece v okruhu tisíců a tisíců kilometrů se
odehrává děsný a bezoddyšný útok; Daimon na své Magnetové hoře řídí příšerně
tiché bubnové bombardement celého světa; mílovými kmity si razí letící vlny
cestu rozlohami, aby zachytily a rozmetaly první prášek Krakatitu kdekoliv na
zemi. A tady v hlubině noci, uprostřed té bledé záplavy světla, pracuje
zarytý, šílený člověk, skloněný nad tajemným procesem přeměny – „Tomši,
pozor,“ vykřikl Prokop; ale jeho hlas zapadl ve tmě jako kámen hozený do tůně
dětskou rukou.
Vyskočil třesa se hrůzou a zimou a prchal dál, jen dál od Grottupu. Zabředl do
mokřiny a stanul; neozve se výbuch? Ne, ticho; a v novém poryvu hrůzy běžel
Prokop do svahu, klopýtal, svezl se na kolena, vyskočil a uháněl; zapadl v
houští, hmatal rukama, prodíral se naslepo, sklouzl a sjížděl dolů; zvedl se,
utíral pot krvácejícíma rukama a utíkal dále.
Uprostřed polí našel něco světlého; hmatal na to, byl to poražený kříž. Těžce
sípaje usedl na prázdný podstavec. Mlžná záplava nad Grottupem je už daleko,
docela daleko na obzoru; je to jen nízké záření nad zemí. Prokop zhluboka
oddychoval; nic, ticho; tedy snad selhalo Tomšovi a nestane se to strašlivé.
Úzkostně naslouchal do dálky; nic, jen studené odkapávání vody v nějaké
podzemní stružce; nic, jenom tlukoucí srdce –
Tu vyrazila nad Grottupem obrovská černá masa, vše zhaslo; jako by se tma
roztrhla, vyšlehl vteřinu nato ohnivý sloup, strašlivě zaplál a rozhodil
kyklopskou hradbu dýmu; a tu již zadul hučící náraz vzduchu, něco zapraskalo,
stromy skřípavě zašuměly, a prásk! děsné švihnutí bičem, rachot, burácející
úder a dunění; země se chvěje a ve vzduchu šíleně víří urvané listí. Lapaje po
dechu, drže se oběma rukama podstavce kříže, aby ho to nesmetlo, poulí Prokop
oči do sršící výhně. I rozštípne se země mocí ohňovou, a v rachotu hromu
promluví Pán.
Ráz na ráz se vyvalil druhý a třetí masiv, roztrhl se rudým šlehnutím a zaplál
třetí, nejhroznější výbuch; patrně chytly sklady. Nějaká hořící masa letí do
nebe, rozprskne se a snáší se deštěm explodujících jisker. Zadul nesmírný
praštící rachot a mění se v bubnovou palbu; ve skladech explodují zápalné
rakety a srší jako jiskry pod bušícím kladivem. Rozlil se brunátný oheň
požáru, jenž puká tata rrrtata suchými ranami jako hnízdo mitrajéz. Vyrazil
čtvrtý a pátý výbuch s třeskným zařváním houfnice; požár přeletěl na obě
strany; hoří málem půl obzoru.
Teprve nyní doletěl zoufalý praskot skoseného lesa grottupského; ale už se
přes něj valila nárazová kanonáda hořících skladů. Šestý výbuch se roztrhl
tvrdým ostrým třesknutím; patrně kresylit; vzápětí hlouběji, basově zahoukla
exploze sudů s dynamonem. Bleskem vyletí ohromný planoucí projektil dopolou
nebes; vyšlehne vysoký plamen, zhasne a vyskočí o kus dál, ale teprve po
vteřinách zahlučí rána a zaburácí hromový otřes. Na chvíli ticho, že slyšíš
praskot ohně, jako když se roští láme; nový rachotící a těžký náraz, a nad
grottupskými závody rázem se slehne plamen, nechávaje jen nízké žíření;
prudkým letícím plápolem hoří město Grottup.
Ztuhlý úděsem zvedl se Prokop a klopýtal odtud.


LIII.

Běžel po silnici těžce sípaje; přeběhl vršek kopce a utíkal do údolu; ohnivá
záplava za ním mizela. Mizely věci a stíny zality plynoucí mlhou; bylo to,
jako by vše nehmotně, mátožně odplývalo a bylo unášeno bezbřehou řekou, kde
nezašplouná vlna a racek nezakřičí. Děsil ho vlastní dupot v tichém a
nesmírném odtékání všeho; tu zpomalil, zdusil kroky a putoval nezvučně do
mléčné tmy.
Na silnici před ním se zatřpytilo světélko; chtěl se mu vyhnout, stanul a
váhal. Lampa nad stolem, ohýnek v kamnech, lucerna cestu hledající; nějaká
zmořená můrka v něm třásla křídly po blikajícím světélku. Blížil se loudavě,
jako by si netroufal; postál, hřál se zdálky na vrtivém ohníčku, šel blíž a
bál se, že ho zas vyženou. Zastavil se opodál; je to vůz s plachtovou
střechou, na oji visí rozžatá lucerna a vrhá chvějivé hrstičky světla na
bílého koně, bílé kameny a bílé pně břízek u cesty; a koník má na hubě režný
pytlík a se skloněnou hlavou chroustá oves, má dlouhou stříbrnou hřívu a ocas
nikdy nezastřižený; a u hlav mu stojí drobný stařík, má bílé vousy a stříbrné
vlásky a je také tak režně světlý jako ta plachta na voze; přešlapuje,
přemýšlí, něco si povídá a protahuje mezi prsty běloučkou hřívu koníkovu.
Nyní se obrátil, dívá se nevidomě do tmy a ptá se vratkým hláskem: „To jsi ty,
Prokope? Tak pojď, já už na tebe čekám.“
Prokop se nepodivil, jen se mu bezmezně ulevilo. „Už jdu,“ vydechl, „vždyť
jsem tak běžel!“
Dědeček k němu přistoupil a sáhl mu na kabát. „Jsi celý mokrý,“ řekl káravě.
„Ještě se nastydneš.“
„Dědečku,“ vyhrkl Prokop, „víte, že vyletěl Grottup?“
Stařeček potřásl lítostivě hlavou. „A co lidu to tenkrát zabilo! Uhnal jsi se,
viď? Sedni si na kozlík, já tě dovezu.“ Cupal ke koníkovi a pomalu mu
odvazoval pytlík s ovsem. „Hý, hý, tak už dost,“ šišlal. „Pojedeme, dostali
jsme hosta.“
„Co to vezete pod tou plachtou?“ ptal se Prokop.
Dědeček se k němu obrátil a zasmál se. „Svět,“ řekl. „Tys ještě neviděl svět?“
„Neviděl.“
„Tak ti to ukážu, počkej.“ Uložil pytlík s ovsem do vozu a jal se po jedné
straně odepínat plachtu naprosto nespěchaje. Odhrnul ji, a pod ní byla bedna
se zaskleným kukátkem. „Počkej,“ opakoval a hledal něco na zemi; sebral
větévku, sedl na bobek k svítilně a zapálil snítku, vše pomaličku a důkladně.
„Tak hoř pěkně, hoř,“ domlouval větvičce a chráně ji dlaněmi cupital k bedně,
zvedl víko a rozsvěcoval uvnitř nějakou lampičku. „Já to mám na olej,“
vysvětloval. „Někteří už svítí karbidem, ale… on karbid tuze pálí do očí. A
taky je to taková věc, vybuchne to, a máš to; ještě to může někomu ublížit. A
olej, to je jako v kostele.“ Naklonil se k okénku a mrkal bledýma očkama
dovnitř. „Dost je vidět. To ti je krásné,“ šeptal nadšeně. „Pojď se podívat.
Ale musíš se ohnout, abys byl… maličký… jako děti. Tak.“
Prokop se schýlil ke kukátku. „To je řecký chrám Páně v Girgenti,“ začal
stařík vážně odříkavat, „na ostrově Sicílii; je zasvěcen Bohu čili Junoně
Lacinii. Podívej se na ty sloupy. Ty jsou udělány z tak velkých kusů, že na
každém kameni může jíst celá rodina. Považ si, jaká je to práce. Mám otočit
dál? – Pohled z hory Penegal v Alpách, když zapadá slunce. To ti pak zahoří
sníh takovým krásným a divným světlem, jak je tu uděláno. To je alpské světlo
a ta hora se jmenuje Latemar. Dál? – To je svaté město Benares v Indii; ta
řeka je posvátná a očišťuje hříchy. Tisíce lidí tu našly, co hledaly.“
Byly to důtklivé, pečlivě narýsované obrázky ručně kolorované; barvy trochu
vybledly, papír zažloutl, a přece jim zůstala milá, potěšující pestrost modří,
zelení a žlutí a červené kabátce lidí a čistá blankytnost nebes; a každá
travička byla vykreslena s láskou a pozorností.
„Ta svatá řeka je Ganges,“ dodal starý s úctou a otočil klikou. „A to je
Zahur, nejkrásnější zámek na světě.“
Prokop se zrovna přisál k okénku. Viděl skvostný zámek s lehkými kupolemi,
vysoké palmy a modrý vodotrysk; malinká postavička s pérem na turbanu, v
nachovém kabátci, žlutých plundrách a s tatarskou šavlí zdraví až po zem dámu
v bílých šatech, jež vede na uzdě tančícího koně. „Kde… kde je Zahur?“ šeptá
Prokop.
Dědeček pokrčil rameny. „Tam někde,“ řekl nejistě, „kde je nejkrásněji. Někdo
to najde a někdo ne. Mám otočit?“
„Ještě ne.“
Starý se odklidil dál a hladil koně po kýtě. „Čekej, nonono čekej,“ vykládal
tiše. „Musíme mu ukázat, víš? Ať má radost.“
„Otočte, dědečku,“ prosil Prokop trna. Následoval hamburský přístav, Kreml,
polární krajina se severní září, sopka Krakatau, Brooklynský most, Notre-Dame,
vesnice domorodců z Bornea; Darwinův domek v Downu, bezdrátová stanice v
Poldhu, ulice v Šanghaji, vodopády Viktoriiny, hrad Pernštýn, petrolejové věže
v Baku. „A to je ten výbuch v Grottup,“ vysvětloval stařík; na obrázku se
válely kotouče růžového dýmu vržené až do nadhlavníku sírově žlutým plamenem;
v dýmu i plamenech nemožně visela roztrhaná lidská těla. „Zahynulo při něm
přes pět tisíc lidí. Bylo to veliké neštěstí,“ vzdychl dědeček. „A to je
poslední obrázek. Tak co, viděl jsi svět?“
„Neviděl,“ bručel Prokop omámen.
Starý pokýval zklamaně hlavou. „Ty chceš vidět příliš mnoho. Musíš být dlouho
živ.“ Sfoukl lampičku v kukátku a brumlaje pomalu stahoval plachtu. „Sedni si
na kozlík, pojedeme.“ Sejmul pytel, kterým byl přikryt koník, a položil jej
Prokopovi na ramena. „Aby ti nebyla zima,“ povídal sedaje k němu, vzal do ruky
opratě a tichounce hvízdl. Koník se dal v mírný klus. „Hý! Nono va-lášku,“
zazpíval děda.
Míjela alej bříz a jeřabin, chalupy přikryté duchnou mlhy, kraj spící a
pokojný. „Dědečku,“ vydralo se z Prokopa, „proč se mi to vše stalo?“
„Cože?“
„Proč mne potkalo tolik věcí?“
Starý přemýšlel. „To se jen zdá,“ povídal konečně. „Co člověka potkává,
vychází z něho. To se jen tak z tebe odmotává jako z klubka.“
„To není pravda,“ protestoval Prokop. „Proč jsem potkal princeznu? Dědečku,
vy… vy mne možná znáte. Vždyť já jsem hledal… tu jinou, že? A přece to přišlo
– proč? Tak řekněte!“
Stařík přemítal žmoulaje měkkými rty. „To byla tvá pýcha,“ řekl pomalu. „To
tak někdy na člověka přijde, ani neví jak, ale bylo to v něm. A začne kolem
sebe máchat –“ Ukazoval to bičem, až se koník polekal a začal uhánět. „Prr,
copak? copak?“ zavolal tenkým hláskem na koně. „Vidíš, zrovna tak to je, když
sebou mladý člověk hází; všechno se s ním splaší. A ono není potřeba dělat
velké kousky. Seď a dávej pozor na cestu; taky dojedeš.“
„Dědečku,“ žaloval Prokop mhouře bolestí oči, „jednal jsem špatně?“
„Špatně nešpatně,“ děl starý rozvážně. „Lidem jsi ublížil. S rozumem bys to
nedělal, musí být rozum; a člověk musí myslet, k čemu je každá věc. Třeba…
můžeš stovkou zapálit, nebo zaplatit, co jsi dlužen; když zapálíš, je to jako
větší na pohled, ale… Stejně to máš se ženskými,“ dodal neočekávaně.
„Jednal jsem špatně?“
„Cože?“
„Byl jsem zlý?“
„… Nebylo v tobě čisto. Člověk… má víc myslet nežli cítit. A ty jsi se hrnul
do všeho jako střelený.“
„Dědečku, to dělal Krakatit.“
„Cože?“
„Já… jsem udělal vynález – a z toho –“
„Kdyby to nebylo v tobě, nebylo by to v tvém vynálezu. Všecko dělá člověk sám
ze sebe. Počkej, teď přemýšlej; teď mysli a vzpomeň si, z čeho je ten tvůj
vynález a jak se dělá. Dobře si to rozvaž a pak teprve řekni, co víš. Hý,
nonono pšš!“
Vozík drkotal po chatrné silnici; bílý valášek horlivě pletl nohama natřásaným
a starožitným klusem; světlo tančilo po zemi, po stromech, po kamení, dědeček
poskakoval na kozlíku a tichounce si pozpěvoval. Prokop si tvrdě přemnul čelo.
„Dědečku,“ zašeptal.
„Nu?“
„Já už to nevím!“
„Copak?“
„Já… já už nevím,… jak… se má… dělat… Krakatit!“
„Tak vidíš,“ děl starý spokojeně. „Přece jen jsi něco našel.“


LIV.

Prokopovi bylo, jako by jeli mírnou krajinou jeho dětství; ale bylo příliš
mlhy, a světélko dosahovalo stěží po kraj cesty mžikavými kmity; po obou
stranách silnice pak byl svět neznámý a zamlklý.
„Hohohot,“ ozval se děda, a koník zajel ze silnice rovnou do toho zastřeného,
němého světa. Kola se bořila do měkké trávy; Prokop rozeznával nízký úval, na
obou stranách bezlisté háje a spanilá loučka mezi nimi. „Prrr,“ křikl starý a
pomalu slézal z kozlíku. „Vstávej,“ povídal, „tak už jsme tady.“ Zvolna
odepínal postraňky. „Víš, sem na nás nikdo nepřijde.“
„Kdo?“
„… Četníci. Pořádek být musí… ale oni vždycky chtějí já nevím jaké papíry… a
povolení… a odkud, a kam… Já se v tom ani nevyznám.“ Vypřahal koně a domlouval
mu tiše: „I mlč, dostaneš kousek chleba.“
Prokop, zdřevěnělý jízdou, sestoupil z kozlíku. „Kde to jsme?“
„Tady, co je ta bouda,“ děl starý neurčitě. „Vyspíš se z toho, a bude to.“
Sejmul z oje lucernu a posvítil na prkennou boudičku, byl to seník či co, ale
bylo to staroučké, chatrné a nakloněné. „A já udělám oheň,“ řekl zpěvavě, „a
uvařím ti čaj, a až se vypotíš, bude ti zase dobře.“ Zabalil Prokopa do pytle
a postavil před něj svítilnu. „Počkej, co donesu dříví. Sedni si tady.“ Užuž
šel, ale něco ho napadlo; zajel rukou do kapsy a díval se tázavě na Prokopa.
„Copak, dědečku?“
„Já… nevím… ale kdybys chtěl… Já jsem taky planetář.“ Vylovil ruku z kapsy a
ukázal: mezi prsty mu vykoukla bílá myška s rubínovýma očkama. „Já vím,“
zažvatlal rychle, „ty tomu nevěříš, ale… ta myška je moc hezká – Chtěl bys?“
„Chci.“
„To je dobře,“ zaradoval se starý. „Š-š-š ma-lá, hop!“ Otevřel dlaň, a bílá
myška mu hbitě vyběhla po rukávě na rameno, čichla mu jemně k chlupatému uchu
a schovala se v jeho límci.
„Ta je krásná,“ vydechl Prokop.
Stařík zazářil. „Počkej, co umí,“ a už běžel k vozíku, hrabal v něm a vracel
se s krabicí plnou narovnaných lístků. Zatřepal krabičkou a vyjevil
rozsvětlená očka do prázdna. „Ukaž, myško, ukaž mu jeho lásku.“ Hvízdl mezi
zuby jako netopýr. Myška vyskočila, sjela mu po rukávě a hopla na krabici;
Prokop bez dechu sledoval její růžové tlapičky, jak hledají mezi lístky;
uchopila jeden do zoubků a chtěla jej vytáhnout; jaksi nešel ven, i zatřepala
hlavou a popadla hnedle sousední; povytáhla jej, sedla na bobek a hryzala si
malinké drápky.
„Tak to je tvá láska,“ šeptal starý nadšeně. „Vem si ji.“
Prokop vyňal vysunutý lístek a sklonil se rychle k světlu. Byla to fotografie
děvčete… toho s rozpoutanými vlasy; má obnažen překrásný prs, a tady ty
náruživé, bezedné oči – Prokop ji poznal. „Dědečku,“ zasténal, „to není ona!“
„Ukaž,“ podivil se starý a vzal mu obrázek z ruky. „A-a, to je škoda,“ broukal
lítostivě. „Taková slečna! Lala, Lilitko, to není ona, nanana ks ks ma-lá!“
Zastrčil obrázek a zas tak tichounce zapištěl. Myška se ohlédla rubínovou
zorničkou, popadla zas tamten lístek do zubů a škubala hlavou; ne, nešlo to;
vyňala sousední a začala se podrbávat.
Prokop se chopil obrázku; byla to Anči, venkovský snímek; neví co s rukama, má
nedělní šaty a tak tu pěkně a hloupě stojí – „To není ona,“ šeptal Prokop.
Děda mu vzal obrázek, pohladil jej a jako by mu něco povídal; pohlédl
nespokojeně, smutně na Prokopa a zas tak tenince pískl.
„Zlobíte se?“ ptal se Prokop nesměle.
Starý neřekl nic a zamyšleně hleděl na myšku. Znovu se pokoušela vyjmout ten
zakleslý lístek; ne, není možno; otřepala se a vytáhla cíp sousedního. Byl to
obrázek princezny. Prokop zaúpěl a pustil jej na zem.
Starý se mlčky shýbl a zvedl obrázek.
„Já sám, já sám,“ chraptěl Prokop a hnal se rukou ke krabici. Děda mu zadržel
ruku: „To se nesmí!“
„Ale tam… tam je ona,“ drtil Prokop, „tam je ta pravá!“
„A-a, tam jsou všichni lidé,“ řekl starý a hladil svou krabici. „Teď dostaneš
planetu.“ Zasykl tiše, myška mu vyklouzla z rukávu, vytáhla zelený lístek a
zas byla ta tam, jako střela; patrně ji Prokop poplašil. „Tak si to přečti,“
povídal stařík zavíraje pečlivě krabici. „Já zatím přinesu roští; a už se
netrap.“ Pohladil koníka, uložil krabici na dně vozu a zamířil k háji. Jeho
světlý režný kabát se mihal ve tmě; valášek ho sledoval pohledem, pohodil
hlavou a pustil se za ním. „Ihaha,“ bylo slyšet zpívat dědečka, „ty chceš jít
se mnou? A-a, vida ho! Hoty, hotyhot, ma-lý!“
Zapadli v mlze, a Prokop si vzpomněl na zelený lístek. „Vaše planeta,“ četl u
blikavého plamínku. „Jste člověk šlechetný, srdce dobrého a ve svém povolání
nad jiné učený. Bude vám mnoho protivenství vytrpěti; ale budete-li se
střežiti prudkosti a vysokomyslnosti, dosáhnete vážnosti u svých sousedů a
vynikajícího postavení. Mnoho ztratíte, ale později odměněn budete. Vaše
nešťastné dny jsou úterý a pátek. Saturn conj. b. b. Martis. DEO gratias.“
Dědeček se vynořil ze tmy s náručí plnou větviček a za ním bílá hlava koně.
„Tak co,“ šeptal napjatě a s jakýmsi autorským ostychem, „četl jsi? Je planeta
dobrá?“
„Je, dědečku.“
„Tak vidíš,“ oddychl si stařík uspokojen. „Všechno dopadne dobře. Nu
chválabohu, jen když je to tak.“ Složil hromadu roští a radostně brebentě
rozžehl před boudou ohníček; zas něco kutil ve voze, přinesl kotlík a cupal
pro vodu. „Hned, hned to bude,“ brumlal horlivě. „Vař se, vař, máme tu hosta.“
Pobíhal jako vzrušená hospodyňka; hned tu byl s chlebem a čichaje rozkoší
rozbaloval kousek selské slaniny. „A sůl, sůl,“ pleskl se do čela a zas běžel
k vozu. Konečně se uvelebil u ohníčka, dal Prokopovi větší díl a sám pomalu
žmoulal každé sousto. Prokopovi šel do očí kouř či co, slzel a jedl; a dědeček
každé druhé sousto podával koníkovi, který nad ním skláněl svou ozářenou
lysinu. A teď ho pojednou Prokop poznal závojem slz: vždyť je to ta stará,
vrásčitá tvář, kterou vždycky vídal na dřevěném stropě své laboratoře! Co se
na ni nadíval usínaje! a ráno, když procitl, už nebyla k poznání, a byly to
jen suky a léta a vlhkost a prach –
Dědeček se usmál. „Chutnalo ti? A-a, už zas se kaboní! Ale, ale!“ Naklonil se
nad kotlík. „Už se to vaří.“ Zvedl se s námahou a belhal se k vozu; za chvilku
tu byl s hrnéčky. „Na, podrž si to.“ Prokop si vzal hrnéček; byly na něm
namalovány pomněnky věnčící zlaté jméno „Ludmila“. Četl to dvacetkrát, a
vyhrkly mu slzy. „Dědečku,“ šeptal, „to… je… její jméno?“
Stařík se na něho díval smutnýma, vlídnýma očima. „Abys to teda věděl,“ řekl
tiše, „je.“
„A… najdu ji někdy?“
Dědeček neřekl nic, jen rychle zamžikal. „Ukaž,“ ozval se nejistě, „já ti
naleju.“
Třesoucí se rukou nastavil Prokop hrnéček; a starý mu naléval opatrně tmavého
čaje. „Pij,“ řekl měkce, „pokud je to teplé.“
„Dě-dě-děkuju,“ vzlykal Prokop a upíjel trpkého odvaru.
Starý si zamyšleně hladil dlouhé vlásky. „Je to hořké,“ povídal pomalu, „tuze
hořké, viď? Nechtěl bys kousek cukru?“
Prokop zavrtěl hlavou, svíralo ho to v ústech hořkostí slz, ale v prsou se mu
rozlévalo dobrodějné teplo.
Stařík hlasitě srkal ze svého hrnéčku. „Tak se podívej,“ řekl, aby něco
zamluvil, „co já tu mám namalováno.“ Podal mu svůj hrnéček; byla na něm kotva,
srdce a kříž. „To je víra, láska a naděje. Tak už neplač.“ Stál nad ohněm s
rukama sepjatýma. „Milý, milý,“ mluvil tiše, „už neuděláš to nejvyšší a
nevydáš všechno. Chtěl jsi se roztrhnout samou silou; a zůstaneš celý, a
nespasíš svět ani jej nerozbiješ. Mnoho v tobě zůstane zavřeno jako v kameni
oheň; tak dobrá, je to obětováno. Chtěl jsi dělat příliš veliké věci, a budeš
dělat věci malé. Tak je to dobře.“
Prokop klečel před ohněm a netroufal si zvednouti oči; věděl nyní, že k němu
mluví Bůh Otec.
„Tak je to dobře,“ šeptal.
„Tak je to dobře. Uděláš věci dobré lidem. Kdo myslí na nejvyšší, odvrátil oči
od lidí. Za to jim budeš sloužit.“
„Tak je to dobře,“ vydechl Prokop na kolenou.
„Nu tak vidíš,“ řekl dědeček potěšen a usedl na bobek. „Koukej, načpak je ten
tvůj – jak říkáš tomu vynálezu?“
Prokop zvedl hlavu. „Já… jsem už zapomněl.“
„To je jedno,“ těšil ho starý. „Přijdeš zas na jiné věci. Počkej, co jsem
chtěl říci? Aha. Načpak takový velký výbuch? Ještě tím někomu ublížíš. Ale
hledej a zkoumej; třeba najdeš… no třeba takové pf pf pf,“ ukazoval dědeček
pšukaje měkkými tvářemi, „víš? aby to dělalo jenom puf puf… a pohánělo to
nějakou věc, aby se lidem líp pracovalo. Rozumíš?“
„Vy myslíte,“ mručel Prokop, „nějaký laciný pohon, ne?“
„Laciný, laciný,“ souhlasil starý radostně. „Aby to dalo hodně užitku. A aby
to taky svítilo, a hřálo, víš?“
„Počkejte,“ přemýšlel Prokop, „já nevím – To by se muselo zkusit… z jiného
konce.“
„No právě. Zkusit to z jiného konce, a je to. Nu tak vidíš, hned máš co dělat.
Ale teď toho nech, zítra je taky den. Já ti ustelu.“ Zvedl se a cupal k vozu.
„Ható hot, ma-lý,“ zazpíval, „půjdeme spat.“ Vracel se s hubenou peřinkou pod
hlavu. „Tak pojď,“ řekl, vzal lucernu a vlezl do prkenné kůlničky. „Nu, slámy
je tu dost,“ broukal ustýlaje, „pro všechny tři. Chválabohu.“
Prokop usedl na slámu. „Dědečku,“ vyhrkl vyjeven, „podívejte se!“
„Copak?“
„Tady, na prknech.“ Na každém prkně kůlny bylo napsáno křídou velké písmeno; a
Prokop četl v blikajících kmitech lucerny: K..R..A…..K..A..T..
„To nic, to nic,“ zabreptal dědeček konejšivě a honem stíral písmena čepicí.
„Už je to pryč. Jen si lehni, já tě přikryju pytlem. Tak.“
Postavil se ve dveřích: „Dadada ma-lý,“ zazpíval třesavě; a koník strčil do
dveří své pěkné stříbrné čelo a otíral se hubou o stařečkův kabát. „Tak jdi,
jdi dovnitř,“ kázal mu starý, „a lehni.“
Valášek vešel, hrabal kopyty u druhé stěny a poklekl. „Já si pak lehnu mezi
vás,“ řekl dědeček; „on ti tu koníček nadýchá, a bude ti teplo, tak.“
Sedl si potichu ve dveřích; za ním ještě řeřavěl do tmy zhasínající ohýnek, a
bylo vidět sladké, moudré oči koňovy, jak se po něm oddaně točí; a starý si
něco šeptal, pobroukával a kýval hlavou.
Prokopovi se svíraly oči mrazivou něžností. Vždyť je to… vždyť je to můj
nebožtík tatínek, napadlo ho; bože, jak zestárl! má už takový tenký oškubaný
krček –
„Prokope, spíš?“ zašeptal starý.
„Nespím,“ odpověděl Prokop chvěje se láskou.
Tu počal dědeček měkce prozpěvovat divnou a tichou píseň: „Lalala hou, dadada
pán, binkili bunkili hou ta ta…“
Prokop konečně usnul pokojným a posilujícím spánkem beze snů.
EOT;
}
