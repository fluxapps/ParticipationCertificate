<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateTableGUIConfig.php';
require_once './Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php';
/**
 * Class ilParticipationCertificateTableGUI
 * @ilCtrl_isCalledBy ilParticipationCertificateTableGUI: ilParticipationCertificateGUI, ilUIPluginRouterGUI
 */
class ilParticipationCertificateTableGUI {

	CONST CMD_CONTENT = 'content';
	CONST CMD_OVERVIEW = 'overview';
	CONST CMD_PRINT_PDF = 'printpdf';
	CONST CMD_PRINT_PDF_WITHOUT_EMENTORING = 'printpdfwithoutementoring';
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
	 * ilParticipationCertificateTableGUI constructor.
	 */
	public function __construct() {
	global $ilCtrl, $ilTabs, $tpl,$ilToolbar;

		$this->toolbar = $ilToolbar;
		$this->tabs = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();


		$this->groupRefId = (int)$_GET['ref_id'];
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', [ 'ref_id', 'group_id' ]);
	}


	public function executeCommand(){
		$this->tpl->getStandardTemplate();
		$this->initHeader();
		$nextClass = $this->ctrl->getNextClass();
		switch($nextClass){
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_CONTENT);
				$this->tabs->setTabActive(self::CMD_OVERVIEW);
				$this->{$cmd}();
				break;
		}
		$this->tpl->show();
	}


	public function content(){
		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption($this->pl->txt('header_btn_print'));
		$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF));
		$this->toolbar->addButtonInstance($b_print);

		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption($this->pl->txt('header_btn_print_eMentoring'));
		$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF_WITHOUT_EMENTORING));
		$this->toolbar->addButtonInstance($b_print);

		$this->tpl->getStandardTemplate();

		$this->initTable();

		$this->tpl->setContent($this->table->getHTML());
	}

	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass('ilRepositoryGUI'));

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', 'ref_id');
		$this->tabs->addTab('overview', $this->pl->txt('header_overview'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateTableGUI::class,ilParticipationCertificateTableGUI::CMD_CONTENT));

		$this->tabs->addTab('config', $this->pl->txt('header_config'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateGUI::class,ilParticipationCertificateGUI::CMD_DISPLAY));
		$this->tabs->activateTab('config');
	}

	public function printPdf() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
		$twigParser->parseData();
	}

	public function printPdfWithoutMentoring() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId,array(),false);
		$twigParser->parseData();
	}


	public function applyFilter() {
		$this->initTable();

		$this->table->writeFilterToSession();
		$this->table->resetOffset();

		$this->content();
	}


	public function resetFilter() {
		$this->initTable();

		$this->table->resetOffset();
		$this->table->resetFilter();

		$this->content();
	}

	protected function initTable($override = false) {

		$this->table =  new ilParticipationCertificateTableGUIConfig($this,ilParticipationCertificateTableGUI::CMD_CONTENT);
	}





}