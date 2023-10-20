<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

require_once('modules/Emails/Emails.php');
require_once('modules/HelpDesk/HelpDesk.php');
require_once('modules/ModComments/ModComments.php');
require_once('modules/Users/Users.php');
require_once('modules/Documents/Documents.php');
require_once ('modules/Leads/Leads.php');
require_once ('modules/Contacts/Contacts.php');
require_once ('modules/Accounts/Accounts.php');
require_once ('modules/ConsultasWeb/ConsultasWeb.php');
require_once 'include/utils/LP_utils.php';
require_once 'vtlib/Vtiger/Functions.php';
require_once 'config.ludere.php';
require_once 'modules/Emails/mail.php';

/**
 * Mail Scanner Action
 */
class Vtiger_MailScannerAction {
	// actionid for this instance
	var $actionid	= false;
	// scanner to which this action is associated
	var $scannerid	= false;
	// type of mailscanner action
	var $actiontype	= false;
	// text representation of action
	var $actiontext	= false;
	// target module for action
	var $module		= false;
	// lookup information while taking action
	var $lookup		= false;

	// Storage folder to use
	var $STORAGE_FOLDER = 'storage/mailscanner/';

	var $recordSource = 'MAIL SCANNER';

	/** DEBUG functionality */
	var $debug		= true;
	function log($message) {
		global $log;
		if($log && $this->debug) { $log->debug($message); }
		else if($this->debug) echo "$message\n";
	}

	/**
	 * Constructor.
	 */
	function __construct($foractionid) {
		$this->initialize($foractionid);
	}

	/**
	 * Initialize this instance.
	 */
	function initialize($foractionid) {
		global $adb;
		$result = $adb->pquery("SELECT * FROM vtiger_mailscanner_actions WHERE actionid=? ORDER BY sequence", Array($foractionid));

		if($adb->num_rows($result)) {
			$this->actionid		= $adb->query_result($result, 0, 'actionid');
			$this->scannerid	= $adb->query_result($result, 0, 'scannerid');
			$this->actiontype	= $adb->query_result($result, 0, 'actiontype');
			$this->module		= $adb->query_result($result, 0, 'module');
			$this->lookup		= $adb->query_result($result, 0, 'lookup');
			$this->actiontext	= "$this->actiontype,$this->module,$this->lookup";
		}
	}

	/**
	 * Create/Update the information of Action into database.
	 */
	function update($ruleid, $actiontext) {
		global $adb;

		$inputparts = explode(',', $actiontext);
		$this->actiontype	= $inputparts[0]; // LINK, CREATE
		$this->module		= $inputparts[1]; // Module name
		$this->lookup		= $inputparts[2]; // FROM, TO

		$this->actiontext = $actiontext;

		if($this->actionid) {
			$adb->pquery("UPDATE vtiger_mailscanner_actions SET scannerid=?, actiontype=?, module=?, lookup=? WHERE actionid=?",
				Array($this->scannerid, $this->actiontype, $this->module, $this->lookup, $this->actionid));
		} else {
			$this->sequence = $this->__nextsequence();
			$adb->pquery("INSERT INTO vtiger_mailscanner_actions(scannerid, actiontype, module, lookup, sequence) VALUES(?,?,?,?,?)",
				Array($this->scannerid, $this->actiontype, $this->module, $this->lookup, $this->sequence));
			$this->actionid = $adb->database->Insert_ID();
		}
		$checkmapping = $adb->pquery("SELECT COUNT(*) AS ruleaction_count FROM vtiger_mailscanner_ruleactions
			WHERE ruleid=? AND actionid=?", Array($ruleid, $this->actionid));
		if($adb->num_rows($checkmapping) && !$adb->query_result($checkmapping, 0, 'ruleaction_count')) {
			$adb->pquery("INSERT INTO vtiger_mailscanner_ruleactions(ruleid, actionid) VALUES(?,?)",
				Array($ruleid, $this->actionid));
		}
	}

	/**
	 * Delete the actions from tables.
	 */
	function delete() {
		global $adb;
		if($this->actionid) {
			$adb->pquery("DELETE FROM vtiger_mailscanner_actions WHERE actionid=?", Array($this->actionid));
			$adb->pquery("DELETE FROM vtiger_mailscanner_ruleactions WHERE actionid=?", Array($this->actionid));
		}
	}

	/**
	 * Get next sequence of Action to use.
	 */
	function __nextsequence() {
		global $adb;
		$seqres = $adb->pquery("SELECT max(sequence) AS max_sequence FROM vtiger_mailscanner_actions", Array());
		$maxsequence = 0;
		if($adb->num_rows($seqres)) {
			$maxsequence = $adb->query_result($seqres, 0, 'max_sequence');
		}
		++$maxsequence;
		return $maxsequence;
	}

	/**
	 * Apply the action on the mail record.
	 */
	function apply($mailscanner, $mailrecord, $mailscannerrule, $matchresult) {
		$this->log(__FUNCTION__);
		$this->log($this->actiontype);
		$this->log($this->module);
		$returnid = false;
		if($mailscanner->_scannerinfo->scannername == 'rodrigo test') {
			//$archivo = fopen('testBuzon.txt', 'a');
			//fwrite($archivo, var_export(array($mailscannerrule), true).PHP_EOL);
		}
		if($mailscanner->_scannerinfo->scannername == 'csdh') {
			//$archivo = fopen('testBuzon.txt', 'a');
			//fwrite($archivo, var_export(array($mailscannerrule, $mailrecord), true).PHP_EOL);
		}
		if($this->actiontype == 'CREATE') {
			if($this->module == 'HelpDesk') {
				$returnid = $this->__CreateTicket($mailscanner, $mailrecord,$mailscannerrule);
			} else if ($this->module == 'Contacts') {
				$returnid = $this->__CreateContact($mailscanner, $mailrecord,$mailscannerrule);
			} else if ($this->module == 'Leads') {
				$returnid = $this->__CreateLead($mailscanner, $mailrecord,$mailscannerrule);
			} else if ($this->module == 'Accounts') {
				$returnid = $this->__CreateAccount($mailscanner, $mailrecord,$mailscannerrule);
			} else if ($this->module == 'ConsultasWeb') {
				$returnid = $this->__CreateConsultasWeb($mailscanner, $mailrecord,$mailscannerrule);
			}
		} else if($this->actiontype == 'LINK') {
			$returnid = $this->__LinkToRecord($mailscanner, $mailrecord);
		} else if ($this->actiontype == 'UPDATE') {
			if ($this->module == 'HelpDesk') {
				$returnid = $this->__UpdateTicket($mailscanner, $mailrecord, $mailscannerrule->hasRegexMatch($matchresult),$mailscannerrule);
				$returnid = false;
			}
		}
		return $returnid;
	}

	/**
	 * Update ticket action.
	 */
	function __UpdateTicket($mailscanner, $mailrecord, $regexMatchInfo,$mailscannerrule) {
		global $adb,$log;
		$log->info("estoy en __UpdateTicket");
		$log->info("el mail record es: ");
		$log->info($mailrecord);

		$this->log(__FUNCTION__);
		$this->log("lookup: " . $this->lookup);
		$returnid = false;
		//$archivo = fopen('testBuzon.txt', 'a');
		//fwrite($archivo, var_export(array($regexMatchInfo), true).PHP_EOL);
		$usesubject = false;
		if($this->lookup == 'SUBJECT') {
			$this->log($regexMatchInfo ? "regexMatchInfo ✔" : "regexMatchInfo ❌");
			// If regex match was performed on subject use the matched group
			// to lookup the ticket record
			//$archivo = fopen('logsBuzon.txt', 'a');
			if($regexMatchInfo) $usesubject = $regexMatchInfo['matches'];
			else $usesubject = $mailrecord->_subject;

			//fwrite($archivo, var_export(array($regexMatchInfo, $usesubject), true));
			// Get the ticket record that was created by SENDER earlier
			$fromemail = $mailrecord->_from[0];

			$this->log("fromemail: $fromemail");

			$linkfocus = $mailscanner->LP_GetTicketRecord($usesubject, $mailrecord->_subject);
			$log->info("el linkfocus es ");
			$log->info($linkfocus);
			//No se usan contactos
			//$commentedBy = $mailscanner->LookupContact($fromemail);
			//if(!$commentedBy) {
				$commentedBy = $mailscanner->LookupAccountAtenciones($fromemail);
			//}

			// If matching ticket is found, update comment, attach email
			if($linkfocus) {
				$this->log("SE ENCONTRÓ TICKET: $usesubject - id: " . $linkfocus->id);
				$this->log("VOY A CREAR UN COMENTARIO");
				$commentFocus = new ModComments();
				$body = $mailrecord->getBodyText();
				$subject = $mailrecord->_subject;
				$commentContent = "Respuesta recibida";
				$commentFocus->column_fields['commentcontent'] = $commentContent;
				$commentFocus->column_fields['related_to'] = $linkfocus->id;
				//con el fin de ser usado a posteriori en el __SaveAttachements
				$commentFocus->column_fields['parent_id'] = $linkfocus->id;
				$commentFocus->column_fields['assigned_user_id'] = $mailscannerrule->assigned_to;
				if($commentedBy) {
					$commentFocus->column_fields['customer'] = $commentedBy;
					$commentFocus->column_fields['from_mailconverter'] = 1;
				} else {
					$commentFocus->column_fields['userid'] = $mailscannerrule->assigned_to;
				}
				$commentFocus->saveentity('ModComments');

				// Set the ticket status to Open if its Closed
				//dado que se van a usar wf uso edicion por recordmodel
				$recordModel = Vtiger_Record_Model::getInstanceById($linkfocus->id);
				//Chequeo la parametrizacion para ver que grupos cortan el flujo
				$sql = "SELECT pt_grupoatw FROM vtiger_parametrizacionesatw 
				INNER JOIN vtiger_crmentity ON crmid = parametrizacionesatwid AND deleted = 0
				ORDER BY pt_grupoatw DESC LIMIT 1 ";
				$res = $adb->pquery($sql);
				$grupos = explode(" |##| ", $res);

				
				foreach ($grupos as $grupo) {
					
					
						if($recordModel->get('ticketcanal') == 'Mail'){
							//si el ticket no esta en el grupo de parametrizacionatw cambio el estado
							if (trim($recordModel->get('ticketgrupo')) != trim($grupo)) {
								$recordModel->set('ticketstatus', 'Reingresado');
								$this->Respuesta_Usuario($recordModel);
							}
						}else $recordModel->set('ticketstatus', 'Devuelto');
						
					
				}

				$recordModel->set('mode', 'edit');
				$recordModel->save();


				

				// Asocio cualquier adjunto al comentario
				//$this->__SaveAttachements($mailrecord, 'ModComments', $commentFocus);

				$this->log("VOY A CREAR EL EMAIL A NIVEL CRM Y VINCULARLO");
				//$log->info($mailrecord." __UpdateTicket HelpDesk ".$linkfocus);
				$idemail = $this->__CreateNewEmail($mailrecord, 'HelpDesk', $linkfocus);
				$log->info("idemail ");
				$log->info($idemail);
				$this->__addRelatedEmail($linkfocus->id, 'HelpDesk', $idemail);
				$this->__addAtencionesEmail($linkfocus->id, $idemail);
				$returnid = $linkfocus->id;
			} else {
				$this->log("NO SE ENCONTRÓ EL TICKET: $usesubject O EL USUARIO DE EMAIL: $fromemail");
				// TODO If matching ticket was not found, create ticket?
				// $returnid = $this->__CreateTicket($mailscanner, $mailrecord);
			}
		}
		return $returnid;
	}

	function __addRelatedEmail($id, $module, $emailid){
		global $adb, $log;
		$log->info("entro a crear la relacion");
		$sql = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES (?,?,?,'Emails')";
		$select = "SELECT 1 FROM vtiger_crmentityrel WHERE crmid = ? AND relcrmid = ?";
		$rs = $adb->pquery($select, array($id, $emailid));
		if($adb->num_rows($rs) == 0){
			$adb->pquery($sql, array($id, $module, $emailid));
		}
	}

	function __addAtencionesEmail($id, $emailid){
		global $adb;
		$sql = "SELECT DISTINCT e.crmid FROM vtiger_atencionesweb a 
			INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0
			INNER JOIN vtiger_crmentityrel er ON er.relcrmid = e.crmid
			WHERE a.aw_estado <> ? AND er.crmid = ?";
		$rs = $adb->pquery($sql, array('Finalizada', $id));
		foreach($rs as $fila){
			$this->__addRelatedEmail($fila[0], 'AtencionesWeb', $emailid);
		}
	}

	function Respuesta_Usuario($entity){
	    global $adb, $log, $site_URL, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID;
	    $log->info('INICIO ' . __FUNCTION__);
	    
	    try {
	        $recordId    = $entity->getId();
	        $topicId     = $entity->get('tickettema');
	        $parentId    = $entity->get('parent_id');
	        $title       = $entity->get('ticket_title');
	        $nroTicket   = $entity->get('ticket_no');
	        $topicName   = Vtiger_Functions::getCRMRecordLabel($topicId);
	        $userName    = Vtiger_Functions::getCRMRecordLabel($parentId);
	        $ownerIdInfo = getRecordOwnerId($recordId);

	        if (!empty($ownerIdInfo['Users'])) {
	            $ownerId   = $ownerIdInfo['Users'];
	            $ownerName = getOwnerName($ownerId);
	            $toEmail   = getUserEmailId('id', $ownerId);
	        }
	        if (!empty($ownerIdInfo['Groups'])) {
	            $ownerId   = $ownerIdInfo['Groups'];
	            $groupInfo = getGroupName($ownerId);
	            $ownerName = $groupInfo[0];
	            $toEmail   = implode(',', getDefaultAssigneeEmailIds($ownerId));
	        }
	        //Para ambientes TESTBPS Y PRODUCCION LR 13/09/23
	        if ($site_URL == '' || strlen($site_URL) < 11){
	        	$site_URL = site_URL;
	        }
	        $linkToTicket = "{$site_URL}index.php?module=HelpDesk&view=Detail&record={$recordId}";
	        //Modificado 04/09/23 LR - Solicitud #150788 redmine
       		$subject = "Vtiger: respuesta $nroTicket";
       		$log->info("the site is: ");
       		$log->info($site_URL);
	        //$subject = "Ticket :: $nroTicket :: Respuesta recibida";
	        /*$body    = "Hola
	                <br>
	                <p>Asunto: $title</p>
	                <br>
	                <p>Ticket $nroTicket fue respondido por el usuario</p>
	                <br>
	                <br>
	                <p>Para ir al ticket haz click aquí <a href=\"$linkToTicket\">$linkToTicket</a></p>
	                <br>
	                Gracias
	            ";*/
	        $body = "
	            <br>
	            <p>Vtiger: respuesta $nroTicket</p>
	            <br>
	            <p>Tienes una respuesta en el ticket $nroTicket </p>
	            <br>
	            <br>
	            <p>Para acceder al ticket haz click aqui; <a href=\"$linkToTicket\">$linkToTicket</a></p>
	            <br>
	        "; 
	        $body = utf8_decode($body);

	        $body = htmlentities($body, ENT_QUOTES, 'UTF-8');
	        $result = send_mail("HelpDesk", $toEmail, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $subject, $body);
	        $log->debug($result == 1 ? "📧 ENVIADO CORRECTAMENTE ✔" : "❌ AL ENVIAR 📧");
	        $log->info('FIN ' . __FUNCTION__);
	    } catch (Exception $e) {
	        $log->fatal('❌ - Error ' . __FUNCTION__);
	        $log->debug($e->getMessage());
	    }catch (Error $e) {
		    $log->fatal('❌ - Error ' . __FUNCTION__);
	        $log->debug($e->getMessage());
		}
	}

	/**
	 * Create ticket action.
	 */
	function __CreateContact($mailscanner, $mailrecord, $mailscannerrule) {
		if($mailscanner->LookupContact($mailrecord->_from[0])) {
			$this->lookup = 'FROM';
			return $this->__LinkToRecord($mailscanner, $mailrecord);
		}
		$name = $this->getName($mailrecord);
		$email = $mailrecord->_from[0];
		$description = $mailrecord->getBodyText();

		$contact = new Contacts();
		$this->setDefaultValue('Contacts', $contact);
		$contact->column_fields['firstname'] = $name[0];
		$contact->column_fields['lastname'] = $name[1];
		$contact->column_fields['email'] = $email;
		$contact->column_fields['assigned_user_id'] = $mailscannerrule->assigned_to;
		$contact->column_fields['description'] = $description;
		$contact->column_fields['source'] = $this->recordSource;

		try {
			$contact->save('Contacts');

			$this->__SaveAttachements($mailrecord, 'Contacts', $contact);
			return $contact->id;
		} catch (Exception $e) {
			//TODO - Review
			return false;
		}
	}

	/**
	 * Create Lead action.
	 */
	function __CreateLead($mailscanner, $mailrecord, $mailscannerrule) {
		if($mailscanner->LookupLead($mailrecord->_from[0])) {
			$this->lookup = 'FROM';
			return $this->__LinkToRecord($mailscanner, $mailrecord);
		}
		$name = $this->getName($mailrecord);
		$email = $mailrecord->_from[0];
		$description = $mailrecord->getBodyText();

		$lead = new Leads();
		$this->setDefaultValue('Leads', $lead);
		$lead->column_fields['firstname'] = $name[0];
		$lead->column_fields['lastname'] = $name[1];
		$lead->column_fields['email'] = $email;
		$lead->column_fields['assigned_user_id'] = $mailscannerrule->assigned_to;
		$lead->column_fields['description'] = $description;
		$lead->column_fields['source'] = $this->recordSource;

		try {
			$lead->save('Leads');

			$this->__SaveAttachements($mailrecord, 'Leads', $lead);

			return $lead->id;
		} catch (Exception $e) {
			//TODO - Review
			return false;
		}
	}

	/**
	 * Create Account action.
	 */
	function __CreateAccount($mailscanner, $mailrecord, $mailscannerrule) {
		if($mailscanner->LookupAccount($mailrecord->_from[0])) {
			$this->lookup = 'FROM';
			return $this->__LinkToRecord($mailscanner, $mailrecord);
		}
		$name = $this->getName($mailrecord);
		$email = $mailrecord->_from[0];
		$description = $mailrecord->getBodyText();

		$account = new Accounts();
		$this->setDefaultValue('Accounts', $account);
		$account->column_fields['accountname'] = $name[0].' '.$name[1];
		$account->column_fields['email1'] = $email;
		$account->column_fields['assigned_user_id'] = $mailscannerrule->assigned_to;
		$account->column_fields['description'] = $description;
		$account->column_fields['source'] = $this->recordSource;

		try {
			$account->save('Accounts');
			$this->__SaveAttachements($mailrecord, 'Accounts', $account);

			return $account->id;
		} catch (Exception $e) {
			//TODO - Review
			return false;
		}
	}

	/**
	 * Create ticket action.
	 */
	function __CreateTicket($mailscanner, $mailrecord, $mailscannerrule) {
		// Prepare data to create trouble ticket
		$usetitle = $mailrecord->_subject;
		$description = $mailrecord->getBodyText();

		// There will be only on FROM address to email, so pick the first one
		$fromemail = $mailrecord->_from[0];
		$contactLinktoid = $mailscanner->LookupContact($fromemail);
		if(!$contactLinktoid) {
			$contactLinktoid = $this-> __CreateContact($mailscanner, $mailrecord, $mailscannerrule);
		}
		if ($contactLinktoid)
			$linktoid = $mailscanner->getAccountId($contactLinktoid);
		if(!$linktoid)
			$linktoid = $mailscanner->LookupAccount($fromemail);

		// Create trouble ticket record
		$ticket = new HelpDesk();
		$this->setDefaultValue('HelpDesk', $ticket);
		if(empty($ticket->column_fields['ticketstatus']) || $ticket->column_fields['ticketstatus'] == '?????')
			$ticket->column_fields['ticketstatus'] = 'Open';
		$ticket->column_fields['ticket_title'] = $usetitle;
		$ticket->column_fields['description'] = $description;
		$ticket->column_fields['assigned_user_id'] = $mailscannerrule->assigned_to;
		if ($contactLinktoid)
			$ticket->column_fields['contact_id'] = $contactLinktoid;
		if ($linktoid)
			$ticket->column_fields['parent_id'] = $linktoid;

		$ticket->column_fields['source'] = $this->recordSource;

		try {
			$ticket->save('HelpDesk');

			// Associate any attachement of the email to ticket
			$this->__SaveAttachements($mailrecord, 'HelpDesk', $ticket);

			if($contactLinktoid)
				$relatedTo = $contactLinktoid;
			else
				$relatedTo = $linktoid;
			$this->linkMail($mailscanner, $mailrecord, $relatedTo);

			return $ticket->id;
		} catch (Exception $e) {
			//TODO - Review
			return false;
		}
	}

	/**
	 * Function to link email record to contact/account/lead
	 * record if exists with same email id
	 * @param type $mailscanner
	 * @param type $mailrecord
	 */
	function linkMail($mailscanner, $mailrecord, $relatedTo) {
		$fromemail = $mailrecord->_from[0];

		$linkfocus = $mailscanner->GetContactRecord($fromemail, $relatedTo);
		$module = 'Contacts';
		if(!$linkfocus) {
			$linkfocus = $mailscanner->GetAccountRecord($fromemail, $relatedTo);
			$module = 'Accounts';
		}

		if($linkfocus) {
			$this->__CreateNewEmail($mailrecord, $module, $linkfocus);
		}
	}

	/**
	 * Add email to CRM record like Contacts/Accounts
	 */
	function __LinkToRecord($mailscanner, $mailrecord) {
		$linkfocus = false;

		$useemail = false;
		if($this->lookup == 'FROM') $useemail = $mailrecord->_from;
		else if($this->lookup == 'TO') $useemail = $mailrecord->_to;

		if ($this->module == 'Contacts') {
			foreach ($useemail as $email) {
				$linkfocus = $mailscanner->GetContactRecord($email);
				if ($linkfocus)
					break;
			}
		} else if ($this->module == 'Accounts') {
			foreach ($useemail as $email) {
				$linkfocus = $mailscanner->GetAccountRecord($email);
				if ($linkfocus)
					break;
			}
		} else if ($this->module == 'Leads') {
			foreach ($useemail as $email) {
				$linkfocus = $mailscanner->GetLeadRecord($email);
				if ($linkfocus)
					break;
			}
		}

		$returnid = false;
		if($linkfocus) {
			$returnid = $this->__CreateNewEmail($mailrecord, $this->module, $linkfocus);
		}
		return $returnid;
	}

	/**
	 * Create new Email record (and link to given record) including attachements
	 */
	function __CreateNewEmail($mailrecord, $module, $linkfocus) {
		global $current_user, $adb;
		if(!$current_user) {
			$current_user = Users::getActiveAdminUser();
		}
		$assignedToId = $linkfocus->column_fields['assigned_user_id'];
		if(vtws_getOwnerType($assignedToId) == 'Groups') {
			$assignedToId = Users::getActiveAdminId();
		}

		$focus = new Emails();
		$focus->column_fields['parent_type'] = $module;
		$focus->column_fields['activitytype'] = 'Emails';
		$focus->column_fields['parent_id'] = "$linkfocus->id@-1|";
		$focus->column_fields['subject'] = $mailrecord->_subject;

		$focus->column_fields['description'] = $mailrecord->getBodyHTML();
		$focus->column_fields['assigned_user_id'] = $assignedToId;
		$focus->column_fields["date_start"] = date('Y-m-d', $mailrecord->_date);
		$focus->column_fields["time_start"] = date('H:i:s', $mailrecord->_date);
		$focus->column_fields["email_flag"] = 'MAILSCANNER';

		$from=$mailrecord->_from[0];
		$to = $mailrecord->_to[0];
		$cc = (!empty($mailrecord->_cc))? implode(',', $mailrecord->_cc) : '';
		$bcc= (!empty($mailrecord->_bcc))? implode(',', $mailrecord->_bcc) : '';
		$flag=''; // 'SENT'/'SAVED'
		//emails field were restructured and to,bcc and cc field are JSON arrays
		$focus->column_fields['from_email'] = $from;
		$focus->column_fields['saved_toid'] = $to;
		$focus->column_fields['ccmail'] = $cc;
		$focus->column_fields['bccmail'] = $bcc;
		$focus->column_fields['source'] = $this->recordSource;
		$focus->save('Emails');

		$emailid = $focus->id;
		$this->log("Created [$focus->id]: $mailrecord->_subject linked it to " . $linkfocus->id);

		// TODO: Handle attachments of the mail (inline/file)
		$this->__SaveAttachements($mailrecord, 'Emails', $focus);

		return $emailid;
	}

	/**
	 * Save attachments from the email and add it to the module record.
	 */
	function __SaveAttachements($mailrecord, $basemodule, $basefocus) {
		global $adb, $log;
		$log->info("estoy en __SaveAttachements");
		//$log->info($mailrecord);
		$log->info($basemodule);
		$log->info($basefocus);
		// If there is no attachments return
		if(!$mailrecord->_attachments) return;

		$userid = $basefocus->column_fields['assigned_user_id'];
		$setype = "$basemodule Attachment";

		$date_var = $adb->formatDate(date('YmdHis'), true);

		foreach($mailrecord->_attachments as $filename=>$filecontent) {
			$attachid = $adb->getUniqueId('vtiger_crmentity');
			$description = $filename;
			$usetime = $adb->formatDate($date_var, true);

			$adb->pquery("INSERT INTO vtiger_crmentity(crmid, smcreatorid, smownerid,
				modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
				Array($attachid, $userid, $userid, $userid, $setype, $description, $usetime, $usetime, 1, 0));

			$issaved = $this->__SaveAttachmentFile($attachid, $filename, $filecontent);
			if($issaved) {
				// Create document record
				$document = new Documents();
				$document->column_fields['notes_title']		= $filename;
				$document->column_fields['filename']		= $filename;
				$document->column_fields['filesize']		= mb_strlen($filecontent, '8bit');
				$document->column_fields['filestatus']		= 1;
				$document->column_fields['filelocationtype']= 'I';
				$document->column_fields['folderid']		= 1; // Default Folder
				$document->column_fields['assigned_user_id']= $userid;
				$document->column_fields['source']			= $this->recordSource;
				$document->save('Documents');

				// Link file attached to document
				$adb->pquery("INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",
					Array($document->id, $attachid));

				// Link document to base record
				$adb->pquery("INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)",
					Array($basefocus->id, $document->id));

				// Link document to Parent entity - Account/Contact/...
				list($eid,$junk)=explode('@',$basefocus->column_fields['parent_id']);
				$adb->pquery("INSERT INTO vtiger_senotesrel(crmid, notesid) VALUES(?,?)",
					Array($eid, $document->id));

				// Link Attachement to the Email
				$adb->pquery("INSERT INTO vtiger_seattachmentsrel(crmid, attachmentsid) VALUES(?,?)",
					Array($basefocus->id, $attachid));
			}
		}
	}

	/**
	 * Save the attachment to the file
	 */
	function __SaveAttachmentFile($attachid, $filename, $filecontent) {
		global $adb,$log;
		$log->info("estoy en __SaveAttachmentFile");
		$dirname = $this->STORAGE_FOLDER;
		if(!is_dir($dirname)) mkdir($dirname);

		$description = $filename;
		$filename = str_replace(' ', '-', $filename);
		$saveasfile = "$dirname$attachid" . "_$filename";
		if(!file_exists($saveasfile)) {

			$this->log("Saved attachement as $saveasfile\n");

			$fh = fopen($saveasfile, 'wb');
			fwrite($fh, $filecontent);
			fclose($fh);
		}

		$mimetype = MailAttachmentMIME::detect($saveasfile);

		$adb->pquery("INSERT INTO vtiger_attachments SET attachmentsid=?, name=?, description=?, type=?, path=?",
			Array($attachid, $filename, $description, $mimetype, $dirname));

		return true;
	}

	function setDefaultValue($module, $moduleObj) {
		if(class_exists('Vtiger_Module_Model')) return;
		$moduleInstance = Vtiger_Module_Model::getInstance($module);
		$fieldInstances = Vtiger_Field_Model::getAllForModule($moduleInstance);
		foreach($fieldInstances as $blockInstance) {
			foreach($blockInstance as $fieldInstance) {
				$fieldName = $fieldInstance->getName();
				$defaultValue = $fieldInstance->getDefaultFieldValue();
				if($defaultValue) {
					$moduleObj->column_fields[$fieldName] = decode_html($defaultValue);
				}
				if($fieldInstance->isMandatory() && !$defaultValue) {
					$moduleObj->column_fields[$fieldName] = Vtiger_Util_Helper::getDefaultMandatoryValue($fieldInstance->getFieldDataType());
				}
			}
		}
	}

	/**
	 * Function to get Mail Sender's Name
	 * @param <Vtiger_MailRecord Object> $mailrecord
	 * @return <Array> containing First Name and Last Name
	 */
	function getName($mailrecord) {
		$name = $mailrecord->_fromname;
		if(!empty($name)) {
			$nameParts = explode(' ', $name);
			if(count($nameParts) > 1) {
				$firstName = $nameParts[0];
				unset($nameParts[0]);
				$lastName = implode(' ', $nameParts);
			} else {
				$firstName = '';
				$lastName = $nameParts[0];
			}
		} else {
			$firstName = '';
			$lastName = $mailrecord->_from[0];
		}

		return array($firstName, $lastName);
	}

	/**
	 * Create ConsultasWeb.
	 */
	function __CreateConsultasWeb($mailscanner, $mailrecord, $mailscannerrule) {
		require_once 'includes/LudereProLoader.php';
		require_once 'includes/Loader.php';
		global $log;
		$log->info("estoy en __CreateConsultasWeb");
		$log->info("El mailscanner ");
		$log->info($mailscanner);
		$log->info("El mailrecord ");
		$log->info($mailrecord);
		$log->info("El mailscannerrule ");
		$log->info($mailscannerrule);
		$name = $this->getName($mailrecord);
		$archivo = fopen('testBuzon.txt', 'a');
		$email = $mailrecord->_from[0];
		$desde = trim(implode(' ', $name));
		$description = $mailrecord->getBodyText();
		$asignado = $mailscannerrule->assigned_to;
		$objeto = $this->parsear(utf8_encode($mailrecord->getBodyText()));
		$para = $mailrecord->_to[0];
		$aweb = new ConsultasWeb();
		$mapConsulta = array(
			"cw_de_mail" => 'email',
			"cw_nombre" => "nombre",
			"cw_asunto" => "motivo",
			//"cw_contenido" => "contenido",
			"cw_asunto" => "motivo",
			"cw_empresa" => "empresa",
			"cw_aportacion" => "aportacion",
			"cw_contribuyente" => "ruc",
			//"cw_telefono" => "telefono",
			//"cw_tema" => "motivo",
			"cw_usuario" => "usuario",
		);
		$this->setDefaultValue('ConsultasWeb', $aweb);
		foreach($mapConsulta as $campo => $valor){
			$aweb->column_fields[$campo] = $objeto->{$valor};	
		}
		
		$grupoid = $asignado;
		$nombreGrupo = '';
		$temaid = null;
		$motivo = str_replace(array('\\r','\\n', '\\t'), array('','',''),$objeto->motivo);
		$tema = false;
		if($objeto->tema) $tema = str_replace(array('\\r','\\n', '\\t'), array('','',''),$objeto->tema);
		//fwrite($archivo, "params => ".var_export(array('tema' => $tema, 'motivo' => $motivo, 'origen' => $desde), true).PHP_EOL);
		$temaModel = Parametrizaciones_Record_Model::getFromTema($tema, $motivo, $desde);
		//fwrite($archivo, "models => ".var_export($temaModel, true).PHP_EOL);
		if($temaModel != null){
			$temaid = $temaModel->get('pt_temavtiger');
			$grupoid = $temaModel->get('pt_grupo');
			$grupoModel = Settings_Groups_Record_Model::getInstance($temaModel->get('pt_grupo'));
			if($grupoModel) $nombreGrupo = $grupoModel->getName();
		}
		//$log->info("information: ".$grupoModel);
		$aweb->column_fields['assigned_user_id'] =  $grupoid;
		$aweb->column_fields['cw_grupo'] = $nombreGrupo;
		$aweb->column_fields['cw_origen'] = $desde;
		$aweb->column_fields['cw_para'] = $para;
		$aweb->column_fields['cw_tema'] = $temaid;
		$aweb->column_fields['cw_contenido'] = $description;

		try {
			$aweb->save('ConsultasWeb');
			global $adb;
			$persona = null;
			if($objeto->documento){
				$regexpas = '/^[a-zA-Z][a-zA-Z]*[0-9][0-9]*/s';
				$regexotros = '/^[0-9][0-9]*[a-zA-Z][a-zA-Z]*[0-9][0-9]*/s';
				if(is_numeric($objeto->documento)){
					$persona = Accounts_Record_Model::getInstanceBySearch(array('acccountry' => 1, 'accdocumenttype' => 'DO', 'accdocumentnumber' => $objeto->documento));
				}else if(preg_match($regexpas, $objeto->documento)){
					$persona = Accounts_Record_Model::getInstanceBySearch(array('acccountry' => 1, 'accdocumenttype' => 'PA', 'accdocumentnumber' => $objeto->documento));
				}else if(preg_match($regexotros, $objeto->documento)){
					$params = array();
					$cadena = $objeto->documento;

					if(strpos($cadena, 'DO')){
						$partes = explode('DO', $cadena);
						$params = array('acccountry' => $partes[0], 'accdocumenttype' => 'DO', 'accdocumentnumber' => $partes[1]);
					}
					if(strpos($cadena, 'PA')){
						$partes = explode('PA', $cadena);
						$params = array('acccountry' => $partes[0], 'accdocumenttype' => 'PA', 'accdocumentnumber' => $partes[1]);
					}
					if(strpos($cadena, 'FR')){
						$partes = explode('FR', $cadena);
						$params = array('acccountry' => $partes[0], 'accdocumenttype' => 'FR', 'accdocumentnumber' => $partes[1]);
					}
					$persona = Accounts_Record_Model::getInstanceBySearch($params);
				}
				if($persona) $persona = $persona->getId();
			}

			if(!$persona){
				$personamodel = Accounts_Record_Model::getUsuarioForName($objeto->documento);
				if($personamodel) $persona = $personamodel->getId();
			}
			include_once('config.ludere.php');
			if(!$persona) $persona = USER_DEFAULT;
			$params = array();
			$campos = array();
			$sql = "UPDATE vtiger_consultasweb SET ";
			$campos[] = " cw_origen = ? ";
			$params[] = $desde;
			$campos[] = " cw_estado = ? ";
			$params[] = 'Pendiente';
			$campos[] = " cw_para = ? ";
			$params[] = $para;
			$campos[] = " cw_contenido = ? ";
			$params[] = $description;
			$campos[] = " cw_persona = ? ";
			$params[] = $persona;
			$campos[] = " cw_grupo = ? ";
			$params[] = $nombreGrupo;
			$campos[] = " cw_tema = ? ";
			$params[] = $temaid;
			foreach($mapConsulta as $campo => $valor){
				$dato = $objeto->{$valor};
				$params[] = str_replace(array('\\r','\\n','\\t'), array('','',''),$dato);
				//$params[] = $objeto->{$valor};
				$campos[] = " $campo = ? "; 
			}
			$sql = $sql . implode(',', $campos) . " WHERE consultaswebid = ?";
			$params[] = $aweb->id;
			$adb->pquery($sql, $params);

			$this->__SaveAttachements($mailrecord, 'ConsultasWeb', $aweb);

			return $aweb->id;
		} catch (Exception $e) {
			//TODO - Review
			fwrite($archivo, var_export($e,true).PHP_EOL);
			return false;
		}
	}

	function parsear($body){
		global $log, $adb;
		$json = json_encode($body, JSON_UNESCAPED_UNICODE);
		$json = utf8_decode($json);
		$return = null;
		$split = explode("Nombre: ", $json);
		$return->nombre = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$split = explode("Documento: ", $json);
		if($split[1]) $return->documento = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$split = explode("Telefono: ", $json);
		if(!$split[1]){
			$split = explode("Teléfono: ", $json);
			$return->telefono = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		}else
			$return->telefono = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$split = explode("Correo Electrónico: ", $json);
		if(!$split[1]){
			$split = explode("Mail: ", $json);
			$return->email = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		}else
			$return->email = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$split = explode("Tema: ", $json);
		$tema = false;
		if($split[1]) $tema = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$return->tema = $tema;
		$categoria = false;
		$split = explode("Categoria Consulta: ", $json);
		if($split[1]) $categoria = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$split = explode("Motivo: ", $json);
		if($split[1]) $categoria = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$return->motivo = $categoria;
		$contenido = false;
		$split = explode("Texto del mensaje:", $json);
		if($split[1])
			$contenido = str_replace(array('\\r\\n', '\\n'), array(PHP_EOL, PHP_EOL),substr($split[1], 0, strlen($split[1]) - 1));
		else{
			$split = explode("Consulta:\\r\\n", $json);
			if($split[1]) $contenido = $contenido = str_replace(array('\\r\\n', '\\n'), array(PHP_EOL, PHP_EOL), trim(substr($split[1], 0, strpos($split[1], "------------"))));
		}
		$return->contenido = $contenido;
		$aportacion = false;
		$split = explode("Tipo Aportación: ", $json);
		if($split[1]) $aportacion = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$return->aportacion = $aportacion;
		$empresa = false;
		$split = explode("Nro Empresa: ", $json);
		if($split[1]) $empresa = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$return->empresa = $empresa;
		$ruc = false;
		$split = explode("Nro Contribuyente: ", $json);
		if($split[1]) $ruc = substr($split[1], 0, strpos($split[1], "\\r\\n"));
		$return->ruc = $ruc;
		$split = explode("Usuario: ", $json);
		if($split[1]){
			$usuario = substr($split[1], 0, strpos($split[1], "\\r\\n"));
			$return->usuario = $usuario;
			if(!$return->documento){
				$return->documento = $usuario;
			}
		}
		$return->email = str_replace('\\t','',$return->email);
		return $return;
	}

}
?>
