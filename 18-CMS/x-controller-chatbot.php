<?php
require_once 'access.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userQuery'])) {
    $userQuery = $_POST['userQuery'];
    $response = getResponse($userQuery);
    saveToDatabase($userQuery, $response);

    header("Location: chatbot.php");
    exit();
}

function getResponse($query)
{
    $keywordsWeights = array(
        // Basic greetings
        'czesc' => 1,
        'dzien dobry' => 1,
        'hejka' => 1,
        'hej' => 1,
        'siema' => 1,
        'witaj' => 1,
        'witam' => 1,
        'dobry wieczor' => 1,

        // Contact information
        'kontakt' => 2,
        'adres' => 2,
        'telefon' => 2,
        'email' => 2,
        'biuro' => 2,
        'kontaktowy' => 2,
        'fax' => 2,

        // Directions and maps
        'nawigacja' => 3,
        'mapa' => 3,
        'jak dojechac' => 3,
        'dojazd' => 3,
        'trasa' => 3,
        'lokalizacja' => 3,
        'gdzie jestescie' => 3,

        // Products and services
        'oferta' => 4,
        'produkty' => 4,
        'uslugi' => 4,
        'cennik' => 4,
        'promocje' => 4,
        'nowosci' => 4,
        'zamowienie' => 4,
        'katalog' => 4,
        'dostepnosc' => 4,
        'gwarancja' => 4,

        // About the company
        'o nas' => 5,
        'firma' => 5,
        'firmie' => 5,
        'kim jestescie' => 5,
        'historia' => 5,
        'misja' => 5,
        'wizja' => 5,
        'kariera' => 5,
        'osiagniecia' => 5,
        'wartosci' => 5,
        'zespol' => 5,

        // Help and support
        'pytanie' => 6,
        'pomoc' => 6,
        'pomocy' => 6,
        'informacja' => 6,
        'wsparcie' => 6,
        'support' => 6,
        'problem' => 6,
        'zwrot' => 6,
        'reklamacja' => 6,
        'serwis' => 6,
        'techniczne' => 6,

        // General and fallback
        '?' => 9999,
        'h' => 9999,
        'help' => 9999,
        'pomoz' => 9999,
        'lista' => 9999
    );

    $normalizedQuery = strtolower(preg_replace('/[^\w]/', '', strtr($query, 'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ', 'acelnoszzACELNOSZZ')));

    $bestKeyword = '';
    $bestKeywordWeight = 0;

    foreach ($keywordsWeights as $keyword => $weight) {
        if (strpos($normalizedQuery, $keyword) !== false) {
            if ($weight > $bestKeywordWeight) {
                $bestKeyword = $keyword;
                $bestKeywordWeight = $weight;
            }
        }
    }

    if (in_array($bestKeyword, ['czesc', 'dzien dobry', 'hejka', 'hej', 'siema', 'witaj', 'witam', 'dobry wieczor'])) {
        return 'Witaj Szanowny Kliencie!';
    }

    switch ($bestKeyword) {
        case 'kontakt':
        case 'adres':
        case 'telefon':
        case 'email':
        case 'biuro':
        case 'kontaktowy':
        case 'fax':
            return getDatabaseInfo('contact');

        case 'nawigacja':
        case 'mapa':
        case 'jak dojechac':
        case 'dojazd':
        case 'trasa':
        case 'lokalizacja':
        case 'gdzie jestescie':
            return getDatabaseInfo('navigation');

        case 'oferta':
        case 'produkty':
        case 'uslugi':
        case 'cennik':
        case 'promocje':
        case 'nowosci':
        case 'zamowienie':
        case 'katalog':
        case 'dostepnosc':
        case 'gwarancja':
            return getDatabaseInfo('offer');

        case 'o nas':
        case 'firma':
        case 'firmie';
        case 'kim jestescie':
        case 'historia':
        case 'misja':
        case 'wizja':
        case 'kariera':
        case 'osiagniecia':
        case 'wartosci':
        case 'zespol':
            return getDatabaseInfo('about');

        case 'pytanie':
        case 'pomoc':
        case 'pomocy':
        case 'informacja':
        case 'wsparcie':
        case 'support':
        case 'problem':
        case 'zwrot':
        case 'reklamacja':
        case 'serwis':
        case 'techniczne':
            return 'Czegokolwiek potrzebujesz, napisz śmiało!';

        case '?':
        case 'h':
        case 'help':
        case 'pomoz':
        case 'lista':
            return 'Mogę pomóc w zakresie kontaktu, nawigacji, oferty i informacji o firmie. Co Cię interesuje?';

        default:
            return 'Jestem tylko początkującym botem i nie znam odpowiedzi na to pytanie.';
    }

}

function getDatabaseInfo($type)
{
    require 'access.php';
    $query = "SELECT details FROM chatbot_info WHERE type=?";
    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['details'];
    } else {
        return "No information available.";
    }
}

function saveToDatabase($question, $answer)
{
    $id_cms = 1;
    require 'access.php';
    $query = "INSERT INTO chatbot (id_cms, question, question_ip, answer) VALUES (?, ?, ?, ?)";
    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
    $stmt = $dbConn->prepare($query);
    $stmt->bind_param("isss", $id_cms, $question, $_SERVER['REMOTE_ADDR'], $answer);
    $stmt->execute();
}
?>