<?php

require_once __DIR__.'/_config.php';

$buildContainer = SpConfig::current()->getBuildContainer();

$ssoState = $buildContainer->getStoreContainer()->getSsoStateStore()->get();

foreach ($ssoState->getSsoSessions() as $ssoSession) {
    print "<ul>\n";
    print "<li>IDP: ".$ssoSession->getIdpEntityId()."</li>\n";
    print "<li>SP: ".$ssoSession->getSpEntityId()."</li>\n";
    print "<li>NameID: ".$ssoSession->getNameId()."</li>\n";
    print "<li>NameIDFormat: ".$ssoSession->getNameIdFormat()."</li>\n";
    print "<li>SessionIndex: ".$ssoSession->getSessionIndex()."</li>\n";
    print "<li>AuthnInstant: ".$ssoSession->getSessionInstant()->format('Y-m-d H:i:s P')."</li>\n";
    print "<li>FirstAuthOn: ".$ssoSession->getFirstAuthOn()->format('Y-m-d H:i:s P')."</li>\n";
    print "<li>LastAuthOn: ".$ssoSession->getLastAuthOn()->format('Y-m-d H:i:s P')."</li>\n";
    print "</ul>\n";
}

if (empty($ssoState->getSsoSessions())) {
    print "<p>No sessions established</p>\n";
}
