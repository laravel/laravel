<?php

namespace Faker\Provider\fr_FR;

use Faker\Calculator\Luhn;

class Company extends \Faker\Provider\Company
{
    /**
     * @var array French company name formats.
     */
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} {{lastName}} {{companySuffix}}',
        '{{lastName}}',
        '{{lastName}}',
    ];

    /**
     * @var array French catch phrase formats.
     */
    protected static $catchPhraseFormats = [
        '{{catchPhraseNoun}} {{catchPhraseVerb}} {{catchPhraseAttribute}}',
    ];

    /**
     * @var array French nouns (used by the catch phrase format).
     */
    protected static $noun = [
        'la sécurité', 'le plaisir', 'le confort', 'la simplicité', "l'assurance", "l'art", 'le pouvoir', 'le droit',
        'la possibilité', "l'avantage", 'la liberté',
    ];

    /**
     * @var array French verbs (used by the catch phrase format).
     */
    protected static $verb = [
        'de rouler', "d'avancer", "d'évoluer", 'de changer', "d'innover", 'de louer', "d'atteindre vos buts",
        'de concrétiser vos projets',
    ];

    /**
     * @var array End of sentences (used by the catch phrase format).
     */
    protected static $attribute = [
        'de manière efficace', 'plus rapidement', 'plus facilement', 'plus simplement', 'en toute tranquilité',
        'avant-tout', 'autrement', 'naturellement', 'à la pointe', 'sans soucis', "à l'état pur",
        'à sa source', 'de manière sûre', 'en toute sécurité',
    ];

    /**
     * @var array Company suffixes.
     */
    protected static $companySuffix = ['SA', 'S.A.', 'SARL', 'S.A.R.L.', 'SAS', 'S.A.S.', 'et Fils'];

    protected static $siretNicFormats = ['####', '0###', '00#%'];

    /**
     * Returns a random catch phrase noun.
     *
     * @return string
     */
    public function catchPhraseNoun()
    {
        return static::randomElement(static::$noun);
    }

    /**
     * Returns a random catch phrase attribute.
     *
     * @return string
     */
    public function catchPhraseAttribute()
    {
        return static::randomElement(static::$attribute);
    }

    /**
     * Returns a random catch phrase verb.
     *
     * @return string
     */
    public function catchPhraseVerb()
    {
        return static::randomElement(static::$verb);
    }

    /**
     * Generates a french catch phrase.
     *
     * @return string
     */
    public function catchPhrase()
    {
        do {
            $format = static::randomElement(static::$catchPhraseFormats);
            $catchPhrase = ucfirst($this->generator->parse($format));

            if ($this->isCatchPhraseValid($catchPhrase)) {
                break;
            }
        } while (true);

        return $catchPhrase;
    }

    /**
     * Generates a siret number (14 digits) that passes the Luhn check.
     *
     * @see http://fr.wikipedia.org/wiki/Syst%C3%A8me_d'identification_du_r%C3%A9pertoire_des_%C3%A9tablissements
     *
     * @return string
     */
    public function siret($formatted = true)
    {
        $siret = self::siren(false);
        $nicFormat = static::randomElement(static::$siretNicFormats);
        $siret .= $this->numerify($nicFormat);
        $siret .= Luhn::computeCheckDigit($siret);

        if ($formatted) {
            $siret = substr($siret, 0, 3) . ' ' . substr($siret, 3, 3) . ' ' . substr($siret, 6, 3) . ' ' . substr($siret, 9, 5);
        }

        return $siret;
    }

    /**
     * Generates a siren number (9 digits) that passes the Luhn check.
     *
     * @see http://fr.wikipedia.org/wiki/Syst%C3%A8me_d%27identification_du_r%C3%A9pertoire_des_entreprises
     *
     * @return string
     */
    public static function siren($formatted = true)
    {
        $siren = self::numerify('%#######');
        $siren .= Luhn::computeCheckDigit($siren);

        if ($formatted) {
            $siren = substr($siren, 0, 3) . ' ' . substr($siren, 3, 3) . ' ' . substr($siren, 6, 3);
        }

        return $siren;
    }

    /**
     * @var array An array containing string which should not appear twice in a catch phrase.
     */
    protected static $wordsWhichShouldNotAppearTwice = ['sécurité', 'simpl'];

    /**
     * Validates a french catch phrase.
     *
     * @param string $catchPhrase The catch phrase to validate.
     *
     * @return bool (true if valid, false otherwise)
     */
    protected static function isCatchPhraseValid($catchPhrase)
    {
        foreach (static::$wordsWhichShouldNotAppearTwice as $word) {
            // Fastest way to check if a piece of word does not appear twice.
            $beginPos = strpos($catchPhrase, $word);
            $endPos = strrpos($catchPhrase, $word);

            if ($beginPos !== false && $beginPos != $endPos) {
                return false;
            }
        }

        return true;
    }

    /**
     * @see http://www.pole-emploi.fr/candidat/le-code-rome-et-les-fiches-metiers-@/article.jspz?id=60702
     *
     * @note Randomly took 300 from this list
     */
    protected static $jobTitleFormat = [
        'Agent d\'accueil',
        'Agent d\'enquêtes',
        'Agent d\'entreposage',
        'Agent de curage',
        'Agro-économiste',
        'Aide couvreur',
        'Aide à domicile',
        'Aide-déménageur',
        'Ambassadeur',
        'Analyste télématique',
        'Animateur d\'écomusée',
        'Animateur web',
        'Appareilleur-gazier',
        'Archéologue',
        'Armurier d\'art',
        'Armurier spectacle',
        'Artificier spectacle',
        'Artiste dramatique',
        'Aspigiculteur',
        'Assistant de justice',
        'Assistant des ventes',
        'Assistant logistique',
        'Assistant styliste',
        'Assurance',
        'Auteur-adaptateur',
        'Billettiste voyages',
        'Brigadier',
        'Bruiteur',
        'Bâtonnier d\'art',
        'Bûcheron',
        'Cameraman',
        'Capitaine de pêche',
        'Carrier',
        'Caviste',
        'Chansonnier',
        'Chanteur',
        'Chargé de recherche',
        'Chasseur-bagagiste',
        'Chef de fabrication',
        'Chef de scierie',
        'Chef des ventes',
        'Chef du personnel',
        'Chef géographe',
        'Chef monteur son',
        'Chef porion',
        'Chiropraticien',
        'Choréologue',
        'Chromiste',
        'Cintrier-machiniste',
        'Clerc hors rang',
        'Coach sportif',
        'Coffreur béton armé',
        'Coffreur-ferrailleur',
        'Commandant de police',
        'Commandant marine',
        'Commis de coupe',
        'Comptable unique',
        'Conception et études',
        'Conducteur de jumbo',
        'Conseiller culinaire',
        'Conseiller funéraire',
        'Conseiller relooking',
        'Consultant ergonome',
        'Contrebassiste',
        'Convoyeur garde',
        'Copiste offset',
        'Corniste',
        'Costumier-habilleur',
        'Coutelier d\'art',
        'Cueilleur de cerises',
        'Céramiste concepteur',
        'Danse',
        'Danseur',
        'Data manager',
        'Dee-jay',
        'Designer produit',
        'Diététicien conseil',
        'Diététique',
        'Doreur sur métaux',
        'Décorateur-costumier',
        'Défloqueur d\'amiante',
        'Dégustateur',
        'Délégué vétérinaire',
        'Délégué à la tutelle',
        'Désamianteur',
        'Détective',
        'Développeur web',
        'Ecotoxicologue',
        'Elagueur-botteur',
        'Elagueur-grimpeur',
        'Elastiqueur',
        'Eleveur d\'insectes',
        'Eleveur de chats',
        'Eleveur de volailles',
        'Embouteilleur',
        'Employé d\'accueil',
        'Employé d\'étage',
        'Employé de snack-bar',
        'Endivier',
        'Endocrinologue',
        'Epithésiste',
        'Essayeur-retoucheur',
        'Etainier',
        'Etancheur',
        'Etancheur-bardeur',
        'Etiqueteur',
        'Expert back-office',
        'Exploitant de tennis',
        'Extraction',
        'Facteur',
        'Facteur de clavecins',
        'Facteur de secteur',
        'Fantaisiste',
        'Façadier-bardeur',
        'Façadier-ravaleur',
        'Feutier',
        'Finance',
        'Flaconneur',
        'Foreur pétrole',
        'Formateur d\'italien',
        'Fossoyeur',
        'Fraiseur',
        'Fraiseur mouliste',
        'Frigoriste maritime',
        'Fromager',
        'Galeriste',
        'Gardien de résidence',
        'Garçon de chenil',
        'Garçon de hall',
        'Gendarme mobile',
        'Guitariste',
        'Gynécologue',
        'Géodésien',
        'Géologue prospecteur',
        'Géomètre',
        'Géomètre du cadastre',
        'Gérant d\'hôtel',
        'Gérant de tutelle',
        'Gériatre',
        'Hydrothérapie',
        'Hématologue',
        'Hôte de caisse',
        'Ingénieur bâtiment',
        'Ingénieur du son',
        'Ingénieur géologue',
        'Ingénieur géomètre',
        'Ingénieur halieute',
        'Ingénieur logistique',
        'Instituteur',
        'Jointeur de placage',
        'Juge des enfants',
        'Juriste financier',
        'Kiwiculteur',
        'Lexicographe',
        'Liftier',
        'Litigeur transport',
        'Logistique',
        'Logopède',
        'Magicien',
        'Manager d\'artiste',
        'Mannequin détail',
        'Maquilleur spectacle',
        'Marbrier-poseur',
        'Marin grande pêche',
        'Matelassier',
        'Maçon',
        'Maçon-fumiste',
        'Maçonnerie',
        'Maître de ballet',
        'Maïeuticien',
        'Menuisier',
        'Miroitier',
        'Modéliste industriel',
        'Moellonneur',
        'Moniteur de sport',
        'Monteur audiovisuel',
        'Monteur de fermettes',
        'Monteur de palettes',
        'Monteur en siège',
        'Monteur prototypiste',
        'Monteur-frigoriste',
        'Monteur-truquiste',
        'Mouleur sable',
        'Mouliste drapeur',
        'Mécanicien-armurier',
        'Médecin du sport',
        'Médecin scolaire',
        'Médiateur judiciaire',
        'Médiathécaire',
        'Net surfeur surfeuse',
        'Oenologue',
        'Opérateur de plateau',
        'Opérateur du son',
        'Opérateur géomètre',
        'Opérateur piquage',
        'Opérateur vidéo',
        'Ouvrier d\'abattoir',
        'Ouvrier serriste',
        'Ouvrier sidérurgiste',
        'Palefrenier',
        'Paléontologue',
        'Pareur en abattoir',
        'Parfumeur',
        'Parqueteur',
        'Percepteur',
        'Photographe d\'art',
        'Pilote automobile',
        'Pilote de soutireuse',
        'Pilote fluvial',
        'Piqueur en ganterie',
        'Pisteur secouriste',
        'Pizzaïolo',
        'Plaquiste enduiseur',
        'Plasticien',
        'Plisseur',
        'Poissonnier-traiteur',
        'Pontonnier',
        'Porion',
        'Porteur de hottes',
        'Porteur de journaux',
        'Portier',
        'Poseur de granit',
        'Posticheur spectacle',
        'Potier',
        'Praticien dentaire',
        'Praticiens médicaux',
        'Premier clerc',
        'Preneur de son',
        'Primeuriste',
        'Professeur d\'italien',
        'Projeteur béton armé',
        'Promotion des ventes',
        'Présentateur radio',
        'Pyrotechnicien',
        'Pédicure pour bovin',
        'Pédologue',
        'Pédopsychiatre',
        'Quincaillier',
        'Radio chargeur',
        'Ramasseur d\'asperges',
        'Ramasseur d\'endives',
        'Ravaleur-ragréeur',
        'Recherche',
        'Recuiseur',
        'Relieur-doreur',
        'Responsable de salle',
        'Responsable télécoms',
        'Revenue Manager',
        'Rippeur spectacle',
        'Rogneur',
        'Récupérateur',
        'Rédacteur des débats',
        'Régleur funéraire',
        'Régleur sur tour',
        'Sapeur-pompier',
        'Scannériste',
        'Scripte télévision',
        'Sculpteur sur verre',
        'Scénariste',
        'Second de cuisine',
        'Secrétaire juridique',
        'Semencier',
        'Sertisseur',
        'Services funéraires',
        'Solier-moquettiste',
        'Sommelier',
        'Sophrologue',
        'Staffeur',
        'Story boarder',
        'Stratifieur',
        'Stucateur',
        'Styliste graphiste',
        'Surjeteur-raseur',
        'Séismologue',
        'Technicien agricole',
        'Technicien bovin',
        'Technicien géomètre',
        'Technicien plateau',
        'Technicien énergie',
        'Terminologue',
        'Testeur informatique',
        'Toiliste',
        'Topographe',
        'Toréro',
        'Traducteur d\'édition',
        'Traffic manager',
        'Trieur de métaux',
        'Turbinier',
        'Téléconseiller',
        'Tôlier-traceur',
        'Vendeur carreau',
        'Vendeur en lingerie',
        'Vendeur en meubles',
        'Vendeur en épicerie',
        'Verrier d\'art',
        'Verrier à la calotte',
        'Verrier à la main',
        'Verrier à main levée',
        'Vidéo-jockey',
        'Vitrier',
    ];
}
