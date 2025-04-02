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
 * DBSelect class
 */
class DBSelect extends DBPDO
{
    // Method to parse, prepare and execute the statement
    public function QueryPDO(PDO $_pdo, ?array $_arg = null, int $_fetchMode = 0, ?array $_options = null) : array{
        $bool = (bool)preg_match('/^select/i', $this->GetStatement());
        if(!$bool){
            $messages = array(
                'en' => '<b>'.strtoupper('select').'</b> statement required.',
                'fr' => 'Requête <b>'.strtoupper('select').'</b> attendue.'
            );

            $this->exception->SetException(new BadMethodCallException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        return $this->DBReturn($bool, $_pdo, $_arg, $_fetchMode, $_options);
    }
}