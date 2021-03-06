<?php
/* Copyright (C) 2003		Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2014	Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004		Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004		Benoit Mortier       <benoit.mortier@opensides.be>
 * Copyright (C) 2005-2012	Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2014		Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2014		Alexandre Spangaro	 <alexandre.spangaro@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 * 		\defgroup   tax		Module salaries
 * 		\brief      Module to include salaries management
 *      \file       htdocs/core/modules/modSalaries.class.php
 *      \ingroup    salaries
 *      \brief      File to activate module salaries
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *	Class to manage salaries module
 */
class modSalaries extends DolibarrModules
{

	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $conf;

		$this->db = $db;
		$this->numero = 510;

		$this->family = "hr";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Employees salaries management";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'dolibarr';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		$this->picto='bill';

		// Data directories to create when module is enabled
		$this->dirs = array("/salaries/temp");

		// Config pages
		$this->config_page_url = array('salaries.php');

		// Dependances
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->langfiles = array("salaries");

		// Constants
		$this->const = array();
		$this->const[0] = array(
				"SALARIES_ACCOUNTING_ACCOUNT_PAYMENT",
				"chaine",
				"421"
		);
		$this->const[1] = array(
				"SALARIES_ACCOUNTING_ACCOUNT_CHARGE",
				"chaine",
				"641"
		);

		// Boxes
		$this->boxes = array();

		// Permissions
		$this->rights = array();
		$this->rights_class = 'salaries';
		$r=0;

		$r++;
		$this->rights[$r][0] = 510;
		$this->rights[$r][1] = 'Read salaries';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';
		$this->rights[$r][5] = '';

		$r++;
		$this->rights[$r][0] = 512;
		$this->rights[$r][1] = 'Create/modify salaries';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'write';
		$this->rights[$r][5] = '';

		$r++;
		$this->rights[$r][0] = 514;
		$this->rights[$r][1] = 'Delete salaries';
		$this->rights[$r][2] = 'd';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delete';
		$this->rights[$r][5] = '';

		$r++;
		$this->rights[$r][0] = 517;
		$this->rights[$r][1] = 'Export salaries';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'export';
		$this->rights[$r][5] = '';


		// Exports
		//--------
		$r=0;
/*
		$r++;
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='Payment of salaries';
		$this->export_permission[$r]=array(array("tax","charges","export"));
		$this->export_fields_array[$r]=array('cc.libelle'=>"Type",'c.rowid'=>"IdSocialContribution",'c.libelle'=>"Label",'c.date_ech'=>'DateDue','c.periode'=>'Period','c.amount'=>"AmountExpected","c.paye"=>"Status",'p.rowid'=>'PaymentId','p.datep'=>'DatePayment','p.amount'=>'AmountPayment','p.num_paiement'=>'Numero');
		$this->export_TypeFields_array[$r]=array('cc.libelle'=>"List:c_chargesociales:libelle:id",'c.libelle'=>"Text",'c.date_ech'=>'Date','c.periode'=>'Period','c.amount'=>"Number","c.paye"=>"Boolean",'p.datep'=>'Date','p.amount'=>'Number','p.num_paiement'=>'Number');
		$this->export_entities_array[$r]=array('cc.libelle'=>"tax_type",'c.rowid'=>"tax",'c.libelle'=>'tax','c.date_ech'=>'tax','c.periode'=>'tax','c.amount'=>"tax","c.paye"=>"tax",'p.rowid'=>'payment','p.datep'=>'payment','p.amount'=>'payment','p.num_paiement'=>'payment');

		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'c_chargesociales as cc, '.MAIN_DB_PREFIX.'chargesociales as c';
		$this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'paiementcharge as p ON p.fk_charge = c.rowid';
		$this->export_sql_end[$r] .=' WHERE c.fk_type = cc.id';
		$this->export_sql_end[$r] .=' AND c.entity = '.$conf->entity;
		*/
	}


	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		global $conf;

		// Clean before activation
		$this->remove($options);

		$sql = array();

		return $this->_init($sql,$options);
	}

    /**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
     */
    function remove($options='')
    {
		$sql = array();

		return $this->_remove($sql,$options);
    }

}
