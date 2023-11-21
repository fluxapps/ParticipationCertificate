<?php
class ilParticipationCertificateGlobalConfigSet extends ActiveRecord {

	const TABLE_NAME = 'dhbw_part_cert_gl_conf';

	public function getConnectorContainerName(): string
    {
		return self::TABLE_NAME;
	}

	/**
	 * @deprecated
	 */
	public static function returnDbTableName(): string
    {
		return self::TABLE_NAME;
	}


	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 * @db_is_primary   true
	 * @db_sequence     true
	 */
	protected ?int $id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected int $order_by = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       1
	 */
	protected int $active = 0;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected string $title = "untitled";

	public function getId(): int
    {
		return $this->id;
	}

	public function setId(int $id): void
    {
		$this->id = $id;
	}

	public function getOrderBy(): int
    {
		return $this->order_by;
	}

	public function setOrderBy(int $order_by): void
    {
		$this->order_by = $order_by;
	}

	public function getTitle(): string
    {
		return $this->title;
	}

	public function setTitle(string $title): void
    {
		$this->title = $title;
	}

	public function getActive(): int
    {
		return $this->active;
	}

	public function setActive(int $active): void
    {
		$this->active = $active;
	}

    /**
     * @param ilParticipationCertificateConfig[] $part_cert_configs
     */
	public static function createNewFromConfigs(array $part_cert_configs): ilParticipationCertificateGlobalConfigSet
    {

		$gl_configs = new ilParticipationCertificateGlobalConfigSets();
		$gl_config = $gl_configs->addNewConfig();
		$gl_config->setTitle("untitled");
		$gl_config->store();


		foreach($part_cert_configs as $config) {
            $file_path_id = 0;
            if($config->getGlobalConfigId() > 0) {
                $file_path_id = $config->getGlobalConfigId() ;
            } else {
                $file_path_id = $config->getGroupRefId();
            }
            $config->setGroupRefId(0);

			switch ($config->getConfigKey()) {
				case 'logo':
					if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
						copy(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::LOGO_FILE_NAME), ilParticipationCertificateConfig::returnPicturePath('absolute', $gl_config->getId(), ilParticipationCertificateConfig::LOGO_FILE_NAME));
					}
					break;
                case 'page1_issuer_signature':
                    if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
                        copy(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME), ilParticipationCertificateConfig::returnPicturePath('absolute', $gl_config->getId(), ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME));
                    }

			}

			$config->setGlobalConfigId($gl_config->getId());
            $config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
            $config->create();
		}

		return $gl_config;
	}
}
