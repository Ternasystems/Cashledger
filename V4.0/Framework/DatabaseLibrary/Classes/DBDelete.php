<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Database\Classes;

use BadMethodCallException;
use PDO;

header('Content-Type: text/html; charset=utf-8');

/*
 * DBDelete class
 */
class DBDelete extends DBPDO
{
    // Method to parse, prepare and execute the statement
    public function QueryPDO(PDO $_pdo, ?array $_arg = null, int $_fetchMode = 0, ?array $_options = null) : void{
        $bool = (bool)preg_match('/^delete/i', $this->GetStatement());
        if (!$bool){
            $messages = array(
                'en' => '<b>'.strtoupper('delete').'</b> statement required.',
                'fr' => 'Requête <b>'.strtoupper('delete').'</b> attendue.'
            );

            $this->exception->SetException(new BadMethodCallException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        $this->DBNoReturn($bool, $_pdo, $_arg, $_options);
    }
}