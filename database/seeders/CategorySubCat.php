<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryType;
use App\Models\CategoryMaster;

class CategorySubCat extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'ICF_kod' => 'd1',
                'name' => 'Lärande och att tillämpa kunskap',
                'description' => 'Lärande, tillämpning av kunskap som är inlärd, tänkande, problemlösning och beslutsfattande.',
                'subcat' => [
                    [
                        'ICF_kod' => 'd 138',
                        'name' => 'Att ta reda på information',
                        'description' => 'Att ta reda på fakta om personer, saker och händelser så som att fråga varför, vad, var, hur att fråga efter namn.',
                    ],
                    [
                        'ICF_kod' => 'd 155',
                        'name' => 'Att förvärva färdigheter ',
                        'description' => 'Att utveckla grundläggande och sammansatta
    förmågor att integrera handlingar eller uppgifter
    som att initiera och fullfölja förvärvandet av en
    färdighet såsom att hantera verktyg eller leksaker
    eller spela spel.'
                    ],
                    [
                        'ICF_kod' => 'd160',
                        'name' => 'Att fokusera uppmärksamhet',
                        'description' => 'Att avsiktligt fokusera på specifika stimuli t.ex.
genom att filtrera bort störande ljud.',
                    ],
                    [
                        'ICF_kod' => 'd175 ',
                        'name' => 'Att lösa problem ',
                        'description' => 'Att finna lösningar på problem eller situationer
genom att identifiera och analysera frågor,
utveckla möjliga lösningar, utvärdera tänkbara
effekter av lösningar och genomföra en vald
lösning såsom att lösa en konflikt mellan två
personer. ',
                    ],
                    [
                        'ICF_kod' => 'd177 ',
                        'name' => 'Att fatta beslut',
                        'description' => 'Att göra ett val mellan alternativ, att förverkliga
valet och utvärdera effekterna av valet såsom att
välja och köpa en specifik sak eller att besluta att ',
                    ],

                ]
            ],

            [
                'ICF_kod' => 'd2',
                'name' => 'llmänna uppgifter och 
                krav ',
                'description' => 'Allmänna aspekter på att genomföra enstaka eller 
                mångfaldiga uppgifter, organisera arbetsgång och 
                hantera stress. Dessa items kan användas 
                tillsammans med mer specifika uppgifter eller 
                handlingar för att identifiera de underliggande 
                grunddragen vid genomförandet av uppgifter 
                under olika omständigheter',
                'subcat' => [
                    [
                        'ICF_kod' => 'd210 ',
                        'name' => 'Att företa en enstaka uppgift',
                        'description' => 'Att genomföra enkla eller komplicerade och 
                        koordinerade handlingar som samman
                        -hänger 
                        med de psykiska och fysiska komponenterna i 
                        en enstaka uppgift såsom att påbörja en uppgift, 
                        att organisera tid, rum och material till 
                        uppgiften, att planera uppgiften stegvis, 
                        genomföra, avsluta och upprätthålla en uppgift',
                    ],
                    [
                        'ICF_kod' => '230 ',
                        'name' => 'Att genomföra daglig rutin',
                        'description' => 'Att genomföra enkla eller sammansatta och 
                        samordnade handlingar för att planera, hantera 
                        och fullfölja vad de dagliga rutinerna kräver såsom 
                        att beräkna tid och göra upp planer för olika 
                        aktiviteter under dagen',
                    ],
                    [
                        'ICF_kod' => 'd240',
                        'name' => 'Att hantera stress och andra 
                        psykologiska krav',
                        'description' => 'Att genomföra enkla eller sammansatta och 
                        samordnade handlingar för att klara och
                        kontrollera de psykologiska krav som ställs för att 
                        genomföra uppgifter som kräver betydande 
                        ansvarstagande och innefattar stress, oro eller kris 
                        såsom när man kör ett fordon i stark trafik eller tar 
                        hand om många barn',
                    ],
                    [
                        'ICF_kod' => 'd298A ',
                        'name' => 'Att hantera sitt eget beteende',
                        'description' => 'Att genomföra enkla eller sammansatta och 
                        samordnade handlingar genom att hantera 
                        beteenden och känslouttryck på ett lämpligt sätt i 
                        förhållande till nya situationer, krav och 
                        förväntningar, såsom att vara tyst på ett bibliotek',
                    ],
                ]
            ],

            [
                'ICF_kod' => 'd3',
                'name' => 'Kommunikation',
                'description' => 'Allmänna och specifika drag i kommunikation 
                genom språk, tecken, symboler och som innefattar 
                att ta emot och förmedla budskap, att genomföra 
                samtal och att använda olika 
                kommunikationsmetoder och 
                kommunikationshjälpmedel.',
                'subcat' => [
                    [
                        'ICF_kod' => 'd310 ',
                        'name' => 'Att kommunicera genom att ta 
                        emot talade meddelanden',
                        'description' => 'Att begripa ordagranna och dolda innebörder i 
                        talade meddelanden såsom att förstå att ett 
                        uttalande påstår ett faktum eller är ett idiomatiskt 
                        uttryck.',
                    ],
                    [
                        'ICF_kod' => 'd315 ',
                        'name' => 'Att kommunicera genom att ta 
                        emot icke-verbala 
                        meddelanden',
                        'description' => 'Att begripa ordagranna eller dolda innebörder i 
                        meddelanden som överbringas genom gester, 
                        symboler och teckningar såsom att förstå att ett 
                        barn är trött när det gnuggar sig i ögonen eller att 
                        varningsljud betyder att det brinner',
                    ],
                    [
                        'ICF_kod' => 'd320',
                        'name' => 'Att kommunicera genom att ta 
                        emot meddelanden på 
                        teckenspråk',
                        'description' => 'Att ta emot och begripa ordagrann och dold 
                        innebörd i meddelanden på tecken-språk',
                    ],
                    [
                        'ICF_kod' => 'd325',
                        'name' => 'Att kommunicera genom att ta 
                        emot skrivna meddelanden',
                        'description' => 'Att begripa ordagrann och dold innebörd i 
                        meddelanden som är överbringade genom skrivet 
                        språk (innefattande punktskrift), såsom att följa 
                        politiska händelser i dagstidning eller att förstå 
                        innebörden i en religiös skrift.',
                    ],
                    [
                        'ICF_kod' => 'd330 ',
                        'name' => 'Att tala',
                        'description' => 'Att åstadkomma ord, fraser eller längre avsnitt i talade meddelanden med ordagrann och dold 
                        innebörd såsom att uttrycka ett faktum eller 
                        berätta en historia muntligt.',
                    ],
                    [
                        'ICF_kod' => 'd335',
                        'name' => 'Att uttrycka sig genom icke-verbala meddelanden',
                        'description' => 'Att använda gester, symboler och teckningar för 
                        att uttrycka meddelanden såsom att skaka på 
                        huvudet för att antyda bristande instämmande 
                        eller att teckna en bild eller diagram för att 
                        uttrycka ett faktum eller en komplex idé.',
                    ],
                    [
                        'ICF_kod' => 'd340 ',
                        'name' => 'Att uttrycka sig genom 
                        meddelanden på teckenspråk',
                        'description' => 'Att uttrycka ordagrann och dold innebörd genom 
                        teckenspråk.',
                    ],
                    [
                        'ICF_kod' => 'd345 ',
                        'name' => 'Att skriva meddelanden',
                        'description' => 'Att förmedla ordagrann och dold innebörd i 
                        meddelanden som uttrycks genom skriftspråk 
                        såsom att skriva ett inbjudningsbrev.',
                    ],
                    [
                        'ICF_kod' => 'd350 ',
                        'name' => 'Konversation',
                        'description' => 'Att starta, hålla igång och slutföra ett utbyte av 
                        tankar och idéer genom talat, skrivet, tecknat eller 
                        annan form av språk med en eller flera personer 
                        som man känner eller som är främmande, i 
                        formella eller tillfälliga miljöer.',
                    ],
                    [
                        'ICF_kod' => 'd360 ',
                        'name' => 'Att använda 
                        kommunikationsutrustningar 
                        och kommunikationstekniker',
                        'description' => 'Att använda utrustningar, tekniker och andra 
                        medel för kommunikation såsom att ringa en vän 
                        på telefon',
                    ],

                ]
            ],
            [
                'ICF_kod' => 'd4',
                'name' => 'Förflyttning',
                'description' => 'Att röra sig genom att ändra kroppsställning 
                eller att förflytta sig från en plats till en annan, 
                att bära, flytta eller hantera föremål, att gå, 
                springa eller klättra och att använda olika 
                former av transportmedel.',
                'subcat' => [
                    [
                        'ICF_kod' => 'd410 ',
                        'name' => 'Att ändra grundläggande 
                        kroppsställning',
                        'description' => 'Att inta eller ändra kroppsställning och att förflytta 
                        sig från en plats till en annan såsom att resa sig ur en stol för att lägga sig på en säng, att lägga sig på 
                        knä eller sätta sig på huk och åter resa sig.',
                    ],
                    [
                        'ICF_kod' => 'd415',
                        'name' => 'Att bibehålla en kroppsställning',
                        'description' => 'g Att vid behov förbli i samma kroppsställning 
                        såsom att förbli sittande eller att förbli stående i 
                        arbete eller skola',
                    ],
                    [
                        'ICF_kod' => 'd420 ',
                        'name' => 'Att förflytta sig själv',
                        'description' => 'Att göra en överflyttning från en yta till en annan 
                        såsom att glida längs en bänk eller flytta sig från 
                        säng till stol utan att ändra kroppsställning',
                    ],
                    [
                        'ICF_kod' => 'd430',
                        'name' => 'Att lyfta och bära föremål',
                        'description' => 'Att lyfta upp ett föremål eller ta något från en plats 
                        till en annan såsom att lyfta en kopp eller leksak 
                        eller att bära en låda eller ett barn från ett rum till 
                        ett annat.',
                    ],
                    [
                        'ICF_kod' => 'd440',
                        'name' => 'Handens finmotoriska 
                        användning',
                        'description' => 'Att genomföra koordinerade handlingar för att 
                        hantera föremål, plocka upp, behandla och släppa 
                        dem genom att använda hand, fingrar och tumme 
                        såsom krävs för att plocka upp ett mynt från ett 
                        bord, slå ett telefonnummer eller trycka på en 
                        knapp.',
                    ],
                    [
                        'ICF_kod' => 'd450 ',
                        'name' => 'Att gå',
                        'description' => 'Att förflytta sig till fots längs en yta, steg för steg, 
                        där en fot alltid är i marken såsom att promenera, 
                        flanera, gå framlänges, baklänges eller i sidled.',
                    ],
                    [
                        'ICF_kod' => 'd455',
                        'name' => 'Att röra sig omkring på olika 
                        sätt',
                        'description' => 'Att förflytta hela kroppen från en plats till en 
                        annan på andra sätt än att gå såsom att klättra 
                        över en sten eller springa utmed en gata, att 
                        skutta, kuta, hoppa, slå kullerbytta och springa 
                        runt hinder',
                    ],
                    [
                        'ICF_kod' => 'd460',
                        'name' => 'Att röra sig omkring på olika 
                        platser',
                        'description' => 'Att gå och förflytta sig på olika platser och 
                        situationer såsom att gå mellan rummen i ett hus, 
                        inom en byggnad eller längs gatan i en stad.',
                    ],

                    [
                        'ICF_kod' => 'd470 ',
                        'name' => 'Att använda transportmedel',
                        'description' => 'Att använda transportmedel för att som 
                        31
                        passagerare förflytta sig såsom att bli körd i en bil 
                        eller buss, riksha, minibuss, på ett fordon draget 
                        av djur, i en privat eller offentlig taxi, buss, tåg, 
                        spårvagn, tunnelbana, båt eller flygplan och 
                        använda människor för transport.',
                    ],
                ]
            ],
            [
                'ICF_kod' => 'd5',
                'name' => 'Personlig vård',
                'description' => 'Egen personlig vård, att tvätta och torka sig själv, 
                att ta hand om sin kropp och kroppsdelar, att klä 
                sig, att äta och dricka och att sköta sin egen hälsa',
                'subcat' => [
                    [
                        'ICF_kod' => 'd510',
                        'name' => 'Att tvätta sig',
                        'description' => 'Att tvätta och torka hela kroppen eller delar av den 
                        genom att använda vatten och lämpliga material 
                        och metoder för att bli ren och torr såsom att 
                        bada, duscha, tvätta händer och fötter, ansikte och 
                        hår och att torka sig
                        .',
                    ],
                    [
                        'ICF_kod' => 'd520 ',
                        'name' => 'Kroppsvård',
                        'description' => 'Att vårda de delar av kroppen som kräver mer än 
                        att tvätta och torka sig såsom hud, ansikte, tänder, 
                        hårbotten, naglar och könsorgan
                        ',
                    ],
                    [
                        'ICF_kod' => 'd530',
                        'name' => 'Att sköta toalettbehov',
                        'description' => 'Att planera och genomföra uttömning av 
                        mänskliga restprodukter (menstruation, urinering 
                        och avföring) och att göra sig ren efteråt.',
                    ],
                    [
                        'ICF_kod' => 'd540 ',
                        'name' => 'Att klä sig',
                        'description' => 'Att genomföra samordnade handlingar och 
                        uppgifter att ta på och av kläder och skodon i 
                        ordning och i enlighet med klimat och sociala 
                        villkor såsom att sätta på sig, rätta till och ta av 
                        skjorta, kjol, blus, underkläder, sari, kimono, tights, 
                        hatt, handskar, kappa, skor, kängor, sandaler och 
                        tofflor',
                    ],
                    [
                        'ICF_kod' => 'd550 ',
                        'name' => 'Att äta',
                        'description' => 'Att genomföra samordnade uppgifter och 
                        handlingar för att äta mat som serveras, att föra 
                        maten till munnen och konsumera den på ett 
                        kulturellt acceptabelt sätt, skära eller bryta maten i 
                        32
                        bitar, öppna flaskor och burkar, använda 
                        matbestick, äta sina måltider till fest och vardags.',
                    ],
                    [
                        'ICF_kod' => 'd560',
                        'name' => 'Att dricka ',
                        'description' => 'Att fatta tag i en dryck som serveras, föra den till 
                        munnen och konsumera den på ett kulturellt 
                        acceptabelt sätt såsom att blanda, röra och hälla 
                        upp dryck för att dricka, öppna flaskor och 
                        burkar, dricka genom sugrör eller rinnande 
                        vatten från en kran eller källa; bröstuppfödning.',
                    ],
                    [
                        'ICF_kod' => 'd570',
                        'name' => 'Att sköta sin egen hälsa',
                        'description' => 'Att tillförsäkra sig fysisk bekvämlighet, hälsa och 
                        fysiskt och psykiskt välbefinnande såsom att 
                        upprätthålla en balanserad diet, lämplig nivå av 
                        fysisk aktivitet, hålla sig varm eller kall, undvika 
                        hälsorisker, ha säkra sexualvanor såsom att 
                        använda kondom, bli vaccinerad och genomgå 
                        regelbundna hälsokontroller',
                    ],
                    [
                        'ICF_kod' => 'd598A ',
                        'name' => 'Att se till sin egen säkerhet',
                        'description' => 'Att undvika risker som kan leda till fysisk skada 
                        eller olycka. Att undvika potentiellt farliga 
                        situationer, såsom att hantera eld på ett felaktigt 
                        sätt eller att springa ut i trafiken',
                    ],
                ]
            ],
            [
                'ICF_kod' => 'd6',
                'name' => 'Hemliv',
                'description' => 'Att genomföra husliga och dagliga sysslor och 
                uppgifter i hemmet. Områden av hemarbete 
                innefattar att skaffa bostad, mat, kläder och andra 
                förnödenheter, hålla rent, reparera och ta hand 
                om personliga och andra hushållsföremål samt 
                hjälpa andra',
                'subcat' => [
                    [
                        'ICF_kod' => 'd610 ',
                        'name' => 'Att skaffa bostad',
                        'description' => 'Att köpa, hyra, möblera och ordna ett rum, ett 
                        hus, en lägenhet eller annan bostad',
                    ],
                    [
                        'ICF_kod' => 'd620 ',
                        'name' => 'Att skaffa varor och tjänster',
                        'description' => 'Att välja, anskaffa och transportera alla varor och 
                        tjänster som krävs för det dagliga livet såsom att 
                        välja, anskaffa, transportera och förvara mat, 
                        33
                        dryck, kläder, rengöringsmaterial, bränsle, 
                        hushållsartiklar, husgeråd, kokkärl, 
                        hushållsredskap och verktyg; att anskaffa 
                        nyttoföremål och andra hushållstjänster.',
                    ],
                    [
                        'ICF_kod' => 'd630',
                        'name' => 'Att bereda måltider',
                        'description' => 'Att planera, organisera, laga och servera enkla och 
                        sammansatta måltider till sig själv och andra 
                        såsom att göra upp en matsedel, välja ut ätlig mat 
                        och dryck och samla ihop ingredienser för att 
                        bereda måltider, laga varm mat och förbereda kall 
                        mat och dryck samt servera maten',
                    ],
                    [
                        'ICF_kod' => 'd640 ',
                        'name' => 'Hushållsarbete',
                        'description' => 'Att klara ett hushåll innefattande att hålla rent i 
                        hemmet, tvätta kläder, använda 
                        hushållsapparater, lagra mat och ta hand om 
                        avfall såsom att sopa, bona, tvätta bänkar, väggar 
                        och andra ytor, samla och kasta avfall, städa rum, 
                        garderober och lådor, samla ihop, tvätta, torka, 
                        vika och stryka kläder, putsa skor, använda 
                        kvastar, borstar och dammsugare, använda 
                        tvättmaskin, torkapparat och strykjärn',
                    ],
                    [
                        'ICF_kod' => 'd6400',
                        'name' => 'Att tvätta och torka kläder',
                        'description' => 'Att tvätta kläder för hand och hänga ut dem att 
                        torka i luften',
                    ],
                    [
                        'ICF_kod' => 'd6401',
                        'name' => 'Att städa köksutrymmen och 
                        köksredskap ',
                        'description' => 'Att städa upp efter matlagning såsom att diska, 
                        rengöra pannor, kastruller och 
                        matlagningsredskap samt städa bord och golv i 
                        matlagnings- och måltidsutrymmen',
                    ],
                    [
                        'ICF_kod' => 'd6402',
                        'name' => 'Att städa bostaden (inkl. kök)',
                        'description' => 'Att städa hemmet såsom att städa upp och 
                        damma, sopa, skura, torka av golv, tvätta fönster 
                        och väggar, rengöra badrum och toaletter och att 
                        rengöra hemmets möbler',
                    ],
                    [
                        'ICF_kod' => 'd6403',
                        'name' => 'Att hantera hushållsapparater',
                        'description' => 'Att använda olika slags hushållsapparater såsom 
                        tvättmaskin, torkapparat, strykjärn, dammsugare 
                        och diskmaskin',
                    ],
                    [
                        'ICF_kod' => 'd6404',
                        'name' => 'Att förvara förnödenheter för 
                        det dagliga livet',
                        'description' => 'Att förvara mat, dryck, kläder och andra 
                        hushållsvaror som behövs för det dagliga livet 
                        innefattande att bereda mat för att konservera, 
                        salta eller frysa, att hålla mat färsk och utom 
                        räckhåll för djur',
                    ],
                    [
                        'ICF_kod' => 'd6405 ',
                        'name' => 'Att avlägsna avfall',
                        'description' => 'Att göra sig av med hushållsavfall såsom att samla 
                        ihop skräp och sopor i hemmet, hantera avfall för 
                        att kasta det, använda anordningar för 
                        avfallshantering, bränna avfall.',
                    ],
                    [
                        'ICF_kod' => 'd6408A ',
                        'name' => 'Att tvätta och torka kläder och 
                        textilier med hushållsapparater',
                        'description' => 'Att tvätta kläder och textilier i tvättmaskin och 
                        torka på lämpligt sätt.',
                    ],
                    [
                        'ICF_kod' => 'd650',
                        'name' => 'Att ta hand om hemmets 
                        föremål',
                        'description' => 'Att underhålla och laga hushållsföremål och andra 
                        personliga föremål innefattande bostaden och 
                        dess inventarier, kläder, material för lek och 
                        rekreation, fordon och hjälpmedel samt att ta 
                        hand om växter och djur, såsom att måla eller 
                        tapetsera rum, laga möbler, reparera rörsystem, 
                        försäkra sig om att fordon fungerar riktigt, vattna 
                        blommor, sköta och ge mat till sällskapsdjur och 
                        husdjur',
                    ],
                    [
                        'ICF_kod' => 'd660',
                        'name' => 'Att bistå andra',
                        'description' => 'Att hjälpa medlemmar av hushållet och andra med 
                        deras lärande, kommunikation, personliga vård, 
                        förflyttning inom eller utanför hemmet och att 
                        vara engagerad i hushållsmedlemmarnas och 
                        andras välbefinnande inom dessa områden.',
                    ],

                ]
            ],
            [
                'ICF_kod' => 'd7',
                'name' => 'Mellanmänskliga 
                interaktioner
                och relationer',
                'description' => 'Detta kapitel handlar om att genomföra de 
                handlingar och uppgifter som behövs för 
                grundläggande och sammansatta interaktioner 
                med människor (okända, vänner, släktingar, 
                familjemedlemmar och andra närstående) på ett i 
                sammanhanget lämpligt och socialt passande sätt.',
                'subcat' => [
                    [
                        'ICF_kod' => 'd720',
                        'name' => 'Sammansatta mellanmänskliga 
                        interaktioner',
                        'description' => 'Att bibehålla och hantera interaktioner med andra 
                        människor på ett i sammanhanget och socialt 
                        lämpligt sätt såsom att reglera känslor och 
                        impulser, reglera verbal och fysisk aggression, 
                        handla oberoende i sociala interaktioner och att 
                        handla i överensstämmelse med sociala regler och 
                        konventioner med exempelvis att leka, studera 
                        eller arbeta med andra.',
                    ],
                    [
                        'ICF_kod' => 'd730 ',
                        'name' => 'Att ha kontakt med okända 
                        personer',
                        'description' => 'Att engagera sig i tillfälliga kontakter och 
                        förbindelser med okända personer för särskilda 
                        syften såsom att fråga efter vägen eller annan 
                        information, eller göra ett inköp.',
                    ],
                    [
                        'ICF_kod' => 'd740 ',
                        'name' => 'Formella relationer',
                        'description' => 'Att skapa och bibehålla specifika relationer i 
                        formella sammanhang såsom med lärare, 
                        arbetsgivare, yrkesutövande eller servicegivare.',
                    ],
                    [
                        'ICF_kod' => 'd750',
                        'name' => 'Informella sociala relationer ',
                        'description' => 'Att ha relationer med andra såsom otvungna 
                        relationer med människor som bor på samma ort 
                        eller med medarbetare, elever, lekkamrater och 
                        människor med likartad bakgrund eller yrke.
                        ',
                    ],
                    [
                        'ICF_kod' => 'd760',
                        'name' => 'Familjerelationer',
                        'description' => 'Att skapa och bibehålla släktskapsrelationer 
                        såsom med medlemmar av kärnfamilj, utvidgad 
                        familj, foster-, adoptiv- och styvfamilj, mer 
                        avlägsna relationer såsom kusiner, sysslingar eller 
                        lagliga vårdnadshavare.',
                    ],
                    [
                        'ICF_kod' => 'd770',
                        'name' => 'Parrelationer*',
                        'description' => 'Att skapa och bibehålla nära eller romantiska 
                        relationer mellan personer såsom mellan man och 
                        hustru, mellan älskande eller sexualpartner.',
                    ],

                ]
            ],
            [
                'ICF_kod' => 'd8',
                'name' => 'Utbildning, arbete, 
                sysselsättning och 
                ekonomiskt liv*',
                'description' => 'Att engagera sig och utföra sådana uppgifter och 
                handlingar som krävs vid utbildning, arbete, 
                anställning och ekonomiska transaktioner.',
                'subcat' => [
                    [
                        'ICF_kod' => 'd839 ',
                        'name' => 'Utbildning, annan specificerad 
                        och ospecificerad',
                        'description' => '',
                    ],
                    [
                        'ICF_kod' => 'd845',
                        'name' => 'Att skaffa, behålla och sluta ett 
                        arbete',
                        'description' => 'Att söka, finna och välja sysselsättning, anställas 
                        och acceptera anställning, bibehålla och avancera i 
                        ett arbete, affärsrörelse, yrke eller profession och 
                        att på ett lämpligt sätt sluta ett arbete.',
                    ],
                    [
                        'ICF_kod' => 'd859 ',
                        'name' => 'Arbete och sysselsättning, 
                        annat specificerat och 
                        ospecificerat',
                        'description' => '',
                    ],
                    [
                        'ICF_kod' => 'd860',
                        'name' => 'Grundläggande ekonomiska 
                        transaktioner',
                        'description' => 'Att engagera sig i varje form av enkel ekonomisk 
                        transaktion såsom att använda pengar för att köpa 
                        mat eller idka byteshandel med varor och tjänster 
                        eller att spara pengar.',
                    ],
                    [
                        'ICF_kod' => 'd865',
                        'name' => 'Komplexa ekonomiska 
                        transaktioner',
                        'description' => 'Att engagera sig i någon form av komplexa 
                        ekonomiska transaktioner som innefattar utbyte 
                        av kapital eller egendom och skapande av 
                        ekonomisk vinst eller värde såsom att köpa en 
                        affär, fabrik eller utrustning, att bibehålla ett 
                        bankkonto, att bedriva handel.',
                    ],
                    [
                        'ICF_kod' => 'd870 ',
                        'name' => 'Ekonomisk självförsörjning',
                        'description' => 'Att ha kontroll över ekonomiska resurser från 
                        privata eller offentliga källor för att försäkra sig om 
                        ekonomisk trygghet för nuvarande och framtida 
                        behov.',
                    ],

                ]
            ],
            [
                'ICF_kod' => 'd9',
                'name' => 'Samhällsgemenskap, socialt 
                och medborgligt liv',
                'description' => 'Detta kapitel handlar om de handlingar och 
                uppgifter som krävs för att engagera sig i 
                organiserat socialt liv utanför familjen, i 
                samhällsgemenskap, socialt och medborgerligt liv.',
                'subcat' => [
                    [
                        'ICF_kod' => 'd920',
                        'name' => 'Rekreation och fritid ',
                        'description' => 'Att engagera sig i någon form av lek eller 
                        fritidssysselsättning som t.ex. informell eller 
                        organiserad lek, spel och sport, program för fysisk 
                        träning, avslappning, nöje eller tidsfördriv, att gå 
                        på konstutställningar, museer, bio eller teater, att 
                        engagera sig i hantverk, hobbys, att läsa för nöjes 
                        skull, att spela musikinstrument, sightseeing, 
                        turism och fritidsresor.',
                    ],
                    [
                        'ICF_kod' => 'd930',
                        'name' => 'Religion och andlighet ',
                        'description' => 'Att engagera sig i religiösa eller andliga aktiviteter, 
                        organisationer och utövande för 
                        självförverkligande, för att finna mening, religiöst 
                        eller andligt värde och för att etablera kontakt 
                        med en gudomlig makt som t.ex. när man går till 
                        kyrka, tempel, moské eller synagoga, när man ber 
                        eller sjunger av religiösa skäl, andlig 
                        kontemplation.',
                    ],

                    [
                        'ICF_kod' => 'd950',
                        'name' => 'Politiskt liv och medborgarskap',
                        'description' => 'Att engagera sig i medborgerligt, socialt, politiskt 
                        och statligt liv, ha medborgerlig legal status samt 
                        att åtnjuta rättigheter, skydd, privilegier och plikter 
                        som sammanhänger med den rollen som t.ex. 
                        rätten att rösta och att vara valbar i politiska val,att bilda politiska sammanslutningar, att åtnjuta 
                        rättigheter och friheter som följer med 
                        medborgarskapet (dvs. rätten till yttrandefrihet, 
                        föreningsfrihet, religionsfrihet, skydd mot att 
                        oskäligt undersökas och gripas, rätten till 
                        rådfrågning, rättegång och andra lagliga 
                        rättigheter), skydd mot diskriminering; att ha laglig 
                        ställning som medborgare.',
                    ],
                ]
            ],
            [
                'ICF_kod' => 'b',
                'name' => 'KROPPSFUNKTIONER 
                - nedsättning inom ',
                'description' => 'Kroppssystemens fysiologiska funktioner, inklusive 
                psykologiska funktioner. ',
                'subcat' => [
                    [
                        'ICF_kod' => 'b1528A ',
                        'name' => 'Känsla av trygghet ',
                        'description' => 'Psykiska funktioner som utifrån situation leder till 
                        känsla/upplevelse av trygghet.',
                    ],

                ]
            ],
            [
                'ICF_kod' => 's',
                'name' => 'KROPPSSTRUKTURER 
                - avvikelse inom ',
                'description' => 'KROPPSSTRUKTURER 
                - avvikelse inom ',
                'subcat' => []
            ],
            [
                'ICF_kod' => 'e',
                'name' => 'OMGIVNINGSFAKTORER',
                'description' => 'Utgör den fysiska, sociala och attitydmässiga 
                omgivning i vilken människor lever och verkar.',
                'subcat' => [
                    [
                        'ICF_kod' => 'e398A',
                        'name' => 'Personligt stöd från person 
                        som vårdar eller stödjer en 
                        närstående',
                        'description' => 'Stöd från personer som vårdar en närstående som 
                        är långvarigt sjuk eller äldre eller som stödjer en 
                        närstående som har funktionshinder. ',
                    ],
                    [
                        'ICF_kod' => 'e598A ',
                        'name' => 'Service, tjänster, system och 
                        policies – Upplevd kvalitet ',
                        'description' => 'Den enskildes eller närståendes upplevelse av 
                        kvalitet i insatser/aktiviteter.',
                    ],
                ]
            ],

            [
                'ICF_kod' => 'deviation',
                'name' => 'Ej utförd insatser', 
                'description' => 'Ej utförd insatser', 
                'subcat' => [
                    [
                        'ICF_kod' => 'deviation 1',
                        'name' => 'Ej utförd insatser subcat', 
                        'description' => 'Ej utförd insatser subcat', 
                    ],
                    
                ]
            ]

            /*[
                'ICF_kod' => 'd2',
                'name' => '', 
                'description' => '', 
                'subcat' => [
                    [
                        'ICF_kod' => '',
                        'name' => '', 
                        'description' => '', 
                    ],
                    
                ]
            ],*/
        ];


        foreach ($data as $key => $category) {
            $categoryMaster = new CategoryMaster;
            $categoryMaster->top_most_parent_id = 1;
            $categoryMaster->created_by = 1;
            $categoryMaster->parent_id = null;
            $categoryMaster->category_type_id = ($category['name']=='Ej utförd insatser') ? 4 : 2;
            $categoryMaster->name = preg_replace('/\s+/', ' ', $category['name']);
            $categoryMaster->category_color = "#ff0000";
            $categoryMaster->is_global = '1';
            $categoryMaster->entry_mode = 'Web';
            $categoryMaster->save();
            foreach ($category['subcat'] as $subcat) {
                $subcategory = new CategoryMaster;
                $subcategory->parent_id = $categoryMaster->id;
                $subcategory->top_most_parent_id = 1;
                $subcategory->created_by = 1;
                $subcategory->category_type_id = ($category['name']=='Ej utförd insatser') ? 4 : 2;
                $subcategory->name = preg_replace('/\s+/', ' ', $subcat['name']);
                $subcategory->category_color = "#ff0000";
                $subcategory->is_global = '1';
                $subcategory->entry_mode = 'Web';
                $subcategory->save();
            }
        }
    }
}
