<?php
set_include_path(__DIR__.'/..' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../Shows.php';
require_once __DIR__.'/../StoredFile.php';
require_once __DIR__.'/../Playlist.php';
require_once __DIR__.'/../Schedule.php';
require_once __DIR__.'/../Preference.php';
require_once __DIR__.'/../RabbitMq.php';
require_once __DIR__.'/../../configs/conf.php';
require_once __DIR__.'/../../../install_minimal/include/AirtimeIni.php';
require_once __DIR__.'/../../../install_minimal/include/AirtimeInstall.php';
require_once __DIR__.'/../../../library/propel/runtime/lib/Propel.php';

Propel::init(__DIR__.'/../../configs/airtime-conf.php');

AirtimeInstall::DbConnect(true);
$sql = "DELETE FROM cc_show";
$CC_DBC->query($sql);
$sql = "DELETE FROM cc_show_days";
$CC_DBC->query($sql);
$sql = "DELETE FROM cc_show_instances";
$CC_DBC->query($sql);

/*
// Create a playlist
$playlist = new Playlist();
$playlist->create("Calendar Load test playlist ".uniqid());

// Add a file
$values = array("filepath" => __DIR__."/test10001.mp3");
$storedFile = StoredFile::Insert($values, false);
$result = $playlist->addAudioClip($storedFile->getId());

// Add a file
$values = array("filepath" => __DIR__."/test10002.mp3");
$storedFile2 = StoredFile::Insert($values, false);

$result = $playlist->addAudioClip($storedFile2->getId());
$result = $playlist->addAudioClip($storedFile2->getId());

echo "Created playlist ".$playlist->getName()." with ID ".$playlist->getId()."\n";
*/
// Create the shows


function createTestShow($showNumber, $showTime, $duration = "1:00")
{
    $data = array();
    $strTime = $showTime->format("Y-m-d H:i");
    echo "Adding show: $strTime\n";
    $data['add_show_name'] = 'automated show '.$showNumber;
    $data['add_show_start_date'] = $showTime->format("Y-m-d");
    $data['add_show_start_time'] = $showTime->format("H:i");
    $data['add_show_duration'] = $duration;
    $data['add_show_no_end'] = 0;
    $data['add_show_repeats'] = 0;
    $data['add_show_description'] = 'automated show';
    $data['add_show_url'] = 'http://www.OfirGal.com';
    $data['add_show_color'] = "";
    $data['add_show_genre'] = "Ofir";
    $data['add_show_background_color'] = "";
    $data['add_show_record'] = 0;
    $data['add_show_hosts'] ="";
    $showId = Show::create($data);
    //echo "show created, ID: $showId\n";

    // populating the show with a playlist
    $instances = Show::getShows($showTime->format("Y-m-d H:i:s"), $showTime->format("Y-m-d H:i:s"));
    $instance = array_pop($instances);
    $show = new ShowInstance($instance["instance_id"]);
    //echo "Adding playlist to show instance ".$show->getShowInstanceId()."\n";
    $show->scheduleShow(array(1));
    //echo "done\n";
    //$show->scheduleShow(array($playlist->getId()));
}

$showTime = new DateTime();

$resolution = "hour";
$showNumber = 1;
$numberOfDays = 180;
$numberOfHours = 0;
$endDate = new DateTime();
$endDate->add(new DateInterval("P".$numberOfDays."DT".$numberOfHours."H"));
echo "End date: ".$endDate->format("Y-m-d H:i")."\n";

while ($showTime < $endDate) {
    echo $showTime->format("Y-m-d H:i")." < " .$endDate->format("Y-m-d H:i")."\n";
    if ($resolution == "minute") {
        createTestShow($showNumber, $showTime, "0:01");	
        $showTime->add(new DateInterval("PT1M"));
    } elseif ($resolution == "hour") {
        createTestShow($showNumber, $showTime);	
        $showTime->add(new DateInterval("PT1H"));
    }
    $showNumber = $showNumber + 1;
}
RabbitMq::PushScheduleFinal();





