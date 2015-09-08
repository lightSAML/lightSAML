<?php

require_once __DIR__.'/_config.php';

$all = SpConfig::current()->getBuildContainer()->getPartyContainer()->getIdpEntityDescriptorStore()->all();
switch (count($all)) {
    case 0:
        print "None IDP configured";
        exit;
    case 1:
        header('Location: login.php?idp='.$all[0]->getEntityID());
        exit;
}

print "<h1>Following IDPs are configured</h1>\n";
print "<p><small>Choose one</small></p>\n";
foreach ($all as $idp) {
    if ($idp->getAllIdpSsoDescriptors()) {
        print "<p><a href=\"login.php?idp={$idp->getEntityID()}\">{$idp->getEntityID()}</a></p>\n";
    }
}
print "\n<p>LigthSAML</p>\n";
