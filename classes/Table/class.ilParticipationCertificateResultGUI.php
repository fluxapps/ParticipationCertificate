<?php

/**
 * Class ilParticipationCertificateResultGUI
 * @ilCtrl_isCalledBy ilParticipationCertificateResultGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls      ilParticipationCertificateResultGUI: ilParticipationCertificateSingleResultGUI, ilParticipationCertificateGUI, ilParticipationCertificateResultModificationGUI
 */
class ilParticipationCertificateResultGUI
{

    const CMD_CONTENT = 'content';
    const CMD_OVERVIEW = 'overview';
    const CMD_PRINT_PDF = 'printpdf';
    const CMD_PRINT_SELECTED_WITHOUTE_MENTORING = 'printSelectedWithouteMentoring';
    const CMD_PRINT_SELECTED = 'printSelected';
    const CMD_INIT_TABLE = 'initTable';
    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilTabsGUI
     */
    protected $tabs;
    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;
    /**
     * @var ilParticipationCertificatePlugin
     */
    protected $pl;
    /**
     * @var int
     */
    protected $groupRefId;
    /**
     * @var
     */
    protected $learnGroup;
    /**
     * @var ilParticipationCertificateAccess
     */
    protected $cert_access;
    /**
     * @var ilLanguage
     */
    protected $lng;

    /**
     * ilParticipationCertificateResultGUI constructor.
     */
    public function __construct()
    {
        global $DIC;

        $this->toolbar = $DIC->toolbar();
        $this->tabs = $DIC->tabs();
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilParticipationCertificatePlugin::getInstance();
        $this->groupRefId = (int) $_GET['ref_id'];
        $this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
	$this->lng = $DIC->language();
	$ementoring=ilParticipationCertificateConfig::getConfig('enable_ementoring',$this->groupRefId);
	if ($ementoring === NULL) {
		$ementoring=true;
		}else {
		$ementoring = boolval($ementoring);
	}
	$this->ementoring = $ementoring;
        /*Access
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if (!$cert_access->hasCurrentUserWriteAccess()) {
            ilUtil::sendFailure($lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }*/

        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, ['ref_id', 'group_id']);
    }

    public function executeCommand()
    {
        $nextClass = $this->ctrl->getNextClass();

        switch ($nextClass) {
            case strtolower(ilParticipationCertificateResultModificationGUI::class):
                $ilparticipationcertificateresultmodificationgui = new ilParticipationCertificateResultModificationGUI();
                $ret1 = $this->ctrl->forwardCommand($ilparticipationcertificateresultmodificationgui);
                break;
            case strtolower(ilParticipationCertificateGUI::class):
                $ilParticipationCertificateGUI = new ilParticipationCertificateGUI();
                $ret2 = $this->ctrl->forwardCommand($ilParticipationCertificateGUI);
                $this->tabs->activateTab(self::CMD_OVERVIEW);
                break;
            case strtolower(ilparticipationcertificatesingleresultgui::class):
                $ilparticipationcertificateresultoverviewgui = new ilParticipationCertificateSingleResultGUI();
                $ret3 = $this->ctrl->forwardCommand($ilparticipationcertificateresultoverviewgui);
                break;
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_CONTENT);
                $this->tabs->activateTab(self::CMD_OVERVIEW);
                switch ($cmd) {
                    case ilParticipationCertificateMultipleResultGUI::CMD_SHOW_ALL_RESULTS:
                        $this->ctrl->forwardCommand(new ilParticipationCertificateMultipleResultGUI());
                        break;
                    case self::CMD_PRINT_PDF:
                    case self::CMD_PRINT_SELECTED:
                    case self::CMD_PRINT_SELECTED_WITHOUTE_MENTORING:
                        $this->{$cmd}();
                        break;
                    default:
                        $this->{$cmd}();
                        break;
                }
                break;
        }
    }

    public function content()
    {
        if (method_exists($this->tpl, 'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
            $this->tpl->getStandardTemplate();
        }
        $this->initHeader();

        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);

	if ($cert_access->hasCurrentUserPrintAccess()) {
	    if ($this->ementoring) {
            	$b_print = ilLinkButton::getInstance();
            	$b_print->setCaption($this->pl->txt('header_btn_print_is_ementoring'), false);
            	$this->ctrl->setParameter($this, 'ementor', true);
            	$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF));
            	$this->toolbar->addButtonInstance($b_print);

            	$b_print = ilLinkButton::getInstance();
            	$this->ctrl->setParameter($this, 'ementor', false);
            	$b_print->setCaption($this->pl->txt('header_btn_print_no_ementoring'), false);
            	$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF));
	    	$this->toolbar->addButtonInstance($b_print);
	    	} else {
            	$b_print = ilLinkButton::getInstance();
            	$this->ctrl->setParameter($this, 'ementor', false);
            	$b_print->setCaption($this->pl->txt('header_btn_print'), false);
            	$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF));
	    	$this->toolbar->addButtonInstance($b_print);
            }
        }

        $this->initTable();

        $this->tpl->setContent($this->table->getHTML());
        if (method_exists($this->tpl, 'printToStdout')) {
            $this->tpl->printToStdout();

        } else {
            $this->tpl->show();
        }
    }

        public function initHeader()
        {
            $this->tpl->setTitle($this->learnGroup->getTitle());
            $this->tpl->setDescription($this->learnGroup->getDescription());
            $this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

            $this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'ref_id', (int) $_GET['ref_id']);
            $this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(array(
                ilRepositoryGUI::class//,
                //ilObjGroupGUI::class
            )));
            $this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, ['ref_id', 'group_id']);
            $this->ctrl->saveParameterByClass(ilParticipationCertificateGUI::class, 'ref_id');

            $this->tabs->addTab(self::CMD_OVERVIEW, $this->pl->txt('header_overview'),
                $this->ctrl->getLinkTargetByClass(self::class, self::CMD_CONTENT));
            $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
            if ($cert_access->hasCurrentUserAdminAccess()) {
                $this->tabs->addTab(ilParticipationCertificateGUI::TAB_CONFIG, $this->pl->txt('header_config'),
                    $this->ctrl->getLinkTargetByClass(ilParticipationCertificateGUI::class,
                        ilParticipationCertificateGUI::CMD_CONFIG));
            }
            $this->tabs->activateTab(self::CMD_OVERVIEW);
        }


    protected function initTable($override = false)
    {
        $this->table = new ilParticipationCertificateResultTableGUI($this, self::CMD_CONTENT);
    }

    public function printPdf()
    {
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserPrintAccess()) {
            $ementor = $_GET['ementor'];
            $usr_id = $_GET['usr_id'];
            $twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, $ementor,
                false);
            $twigParser->parseData();
        } else {
            ilUtil::sendFailure($this->lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }
    }

    public function printSelected()
    {
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserPrintAccess()) {
            if (!isset($_POST['record_ids']) || (isset($_POST['record_ids']) && !count($_POST['record_ids']))) {
                ilUtil::sendFailure($this->pl->txt('no_records_selected'), true);
                $this->ctrl->redirect($this, self::CMD_CONTENT);
            }
            $usr_id = $_POST['record_ids'];
            $twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, true, false,
                false);
            $twigParser->parseData();
        } else {
            ilUtil::sendFailure($this->lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }
    }

    public function printSelectedWithouteMentoring()
    {
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserPrintAccess()) {
            if (!isset($_POST['record_ids']) || (isset($_POST['record_ids']) && !count($_POST['record_ids']))) {
                ilUtil::sendFailure($this->pl->txt('no_records_selected'), true);
                $this->ctrl->redirect($this, self::CMD_CONTENT);
            }

            $usr_id = $_POST['record_ids'];
            $twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, false, false,
                false);
            $twigParser->parseData();
        } else {
            ilUtil::sendFailure($this->lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }
    }

    public function applyFilter()
    {
        $table = new ilParticipationCertificateResultTableGUI($this, self::CMD_CONTENT);
        $table->writeFilterToSession();
        $table->resetOffset();
        $this->ctrl->redirect($this, self::CMD_CONTENT);
    }

    public function resetFilter()
    {
        $table = new ilParticipationCertificateResultTableGUI($this, self::CMD_CONTENT);
        $table->resetOffset();
        $table->resetFilter();
        $this->ctrl->redirect($this, self::CMD_CONTENT);
    }
}

?>
