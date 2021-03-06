<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package UniBooks
 * @author Emiliano Bovetti
 * @since Version 1.0
 */

$lang['db_invalid_connection_str'] = 'Impossibile determinare le impostazioni del database in base alla stringa di connessione inserita.';
$lang['db_unable_to_connect'] = 'Impossibile connettersi al database utilizzando le impostazioni fornite.';
$lang['db_unable_to_select'] = 'Impossibile selezionare questo database: %s';
$lang['db_unable_to_create'] = 'Impossibile creare questo database: %s';
$lang['db_invalid_query'] = 'La query inserita non &egrave; valida.';
$lang['db_must_set_table'] = '&Egrave; necessatio impostare la tabella nella quale eseguire le query.';
$lang['db_must_use_set'] = '&Egrave; necessario usare il metodo "set" per aggiornare una voce.';
$lang['db_must_use_index'] = '&Egrave; necessario specificare un index per abbinare gli aggiornamenti in batch.';
$lang['db_batch_missing_index'] = 'Ad una o più rows presentate per l\'aggiornamento in batch updating manca l\'index.';
$lang['db_must_use_where'] = 'Gli aggiornamenti senza clausola "where" non sono consentiti.';
$lang['db_del_must_use_where'] = 'Le eliminazioni senza clausola "where" o "like" non sono consentite.';
$lang['db_field_param_missing'] = 'Per fare il fetch dei campi &egrave; richiesto il nome della tabella come parametro.';
$lang['db_unsupported_function'] = 'Questa caratteristica non &egrave; disponibile con il database che stai utilizzando.';
$lang['db_transaction_failure'] = 'Errore nella trasizione: Rollback eseguito.';
$lang['db_unable_to_drop'] = 'Impossibile eseguire il drop del database selezionato.';
$lang['db_unsuported_feature'] = 'Caratteristica non supportata dalla piattaforma database in uso.';
$lang['db_unsuported_compression'] = 'Il formato di compressione file scelto non &egrave; supportato dal server.';
$lang['db_filepath_error'] = 'Impossibile scrivere dati nel file path indicato.';
$lang['db_invalid_cache_path'] = 'La cache path indicata non &egrave; valida o non &egrave; scrivibile.';
$lang['db_table_name_required'] = 'Il nome della tabella &egrave; richiesto per questa operazione.';
$lang['db_column_name_required'] = 'Il nome della colonna &egrave; richiesto per questa operazione.';
$lang['db_column_definition_required'] = 'Una definizione della colonna &egrave; richiesta per questa operazione.';
$lang['db_unable_to_set_charset'] = 'Impossibile impostare la il charset per la connessione client: %s';
$lang['db_error_heading'] = 'Si &egrave; verificato un errore nel database';

/* End of file db_lang.php */
/* Location: ./application/language/italian/db_lang.php */
