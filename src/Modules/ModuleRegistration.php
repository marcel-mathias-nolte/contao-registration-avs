<?php

/*
 * This file is part of ExtendedRegistrationBundle.
 *
 * @package   ExtendedRegistrationBundle
 * @author    Marcel Mathias Nolte
 * @copyright Marcel Mathias Nolte 2015-2020
 * @website	  https://github.com/marcel-mathias-nolte
 * @license   LGPL-3.0-or-later
 */



namespace MarcelMathiasNolte\ExtendedRegistrationBundle\Modules;

/**
 * Front end module "registration".
 *
 * @author    Marcel Mathias Nolte
 */
class ModuleRegistration extends \Contao\ModuleRegistration
{
    protected $arrFiles;

    /**
     * Render backend preview, if necessary
     *
     * @return string
     */
    public function generate()
    {
        $this->arrFiles = $_FILES;
        return parent::generate();
    }


    /**
     * Generate the content element
     */
    protected function compile()
    {
        parent::compile();
    }

    /**
     * Send an admin notification e-mail
     *
     * @param integer $intId
     * @param array   $arrData
     */
    protected function sendAdminNotification($intId, $arrData)
    {
        // Prepare the simple token data
        $arrTokenData = $arrData;
        $arrTokenData['domain'] = \Idna::decode(\Environment::get('host'));
        $arrTokenData['link'] = 'https://' . \Idna::decode(\Environment::get('host')) . '/contao?do=member&act=edit&id=' . $intId;
        $objUser = (object)$arrData;
        $objEmail = new \Email();
        $objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
        $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
        $objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['adminSubject'], \Idna::decode(\Environment::get('host')));
        $strData = "\n\n";
        // Add user details
        foreach ($arrData as $k => $v) {
            if ($k == 'password' || $k == 'tstamp' || $k == 'activation' || $k == 'dateAdded') {
                continue;
            }
            $v = \StringUtil::deserialize($v);
            if ($k == 'dateOfBirth' && \strlen($v)) {
                $v = \Date::parse(\Config::get('dateFormat'), $v);
            }
            $strData .= $GLOBALS['TL_LANG']['tl_member'][$k][0] . ': ' . (\is_array($v) ? implode(', ', $v) : $v) . "\n";
        }
        $objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['adminText'], $intId, $strData . "\n") . "\n";
        if (isset($this->arrFiles['ageApproval']) && $this->arrFiles['ageApproval']['size'] > 0) {
            $homeFolderObj = \FilesModel::findByPath('files/altersnachweise');
            $i = 0;
            do {
                $ext = strtolower(@end(explode('.', $this->arrFiles['ageApproval']['name'])));
                $fn = $homeFolderObj->path . '/' . \StringUtil::generateAlias($objUser->username) . ($i > 0 ? '-' . $i : '') . '.' . $ext;
                $i++;
            } while (file_exists(TL_ROOT . '/' . $fn));
            move_uploaded_file($this->arrFiles['ageApproval']['tmp_name'], TL_ROOT . '/' . $fn);
            $objFile = \FilesModel::findByPath($fn);

            if ($objFile !== null) {
                $objFile->tstamp = time();
                $objFile->path = $fn;
                $objFile->hash = md5_file(TL_ROOT . '/' . $fn);
                $objFile->save();
                $uuid = $objFile->uuid;
            } else if (file_exists(TL_ROOT . '/' . $fn)) {
                $objFile = \Dbafs::addResource($fn);
                $uuid = $objFile->uuid;
            }
            $this->Database->prepare("UPDATE tl_member SET ageApproval = ? WHERE id = ?")->execute($uuid, $intId);
            $objEmail->attachFile(TL_ROOT . '/' . $fn);
        }
        $objEmail->sendTo($GLOBALS['TL_ADMIN_EMAIL']);
    }

}
