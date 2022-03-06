<?php

$GLOBALS['TL_DCA']['tl_member']['fields']['dsgvo'] = array
(
    'exclude'                 => true,
    'eval'                    => array('mandatory'=>true, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'personal', 'tl_class'=>'w50'),
    'inputType'               => 'checkbox',
    'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_member']['fields']['ageApproval'] = array
(
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'mandatory'=>true, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'personal',  'tl_class'=>'clr', 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes']),
    'sql'                     => "binary(16) NULL"
);
$GLOBALS['TL_DCA']['tl_member']['fields']['email']['eval']['mandatory'] = false;
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] .= ';{extended_avs_legend},dsgvo,ageApproval;';
