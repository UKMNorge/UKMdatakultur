<?php

UKMwp_innhold::registerFunctions();
UKMdatakultur::addViewData('page', getPage( UKMdatakultur::SLUG ));
UKMdatakultur::addViewData('subpages', UKMdatakultur::getSubpages());