<?php

namespace API_TellerRepositories_Context;

use API_DTORepositories_Context\Context;
use API_TellerRepositories_Collection\CashFigures;
use API_TellerRepositories_Collection\TellerAudits;
use API_TellerRepositories_Collection\TellerCashCounts;
use API_TellerRepositories_Collection\TellerPayments;
use API_TellerRepositories_Collection\TellerReceipts;
use API_TellerRepositories_Collection\TellerReversals;
use API_TellerRepositories_Collection\Tellers;
use API_TellerRepositories_Collection\TellerSessions;
use API_TellerRepositories_Collection\TellerTransactions;
use API_TellerRepositories_Collection\TellerTransfers;
use API_TellerRepositories_Model\CashFigure;
use API_TellerRepositories_Model\Teller;
use API_TellerRepositories_Model\TellerAudit;
use API_TellerRepositories_Model\TellerCashCount;
use API_TellerRepositories_Model\TellerPayment;
use API_TellerRepositories_Model\TellerReceipt;
use API_TellerRepositories_Model\TellerReversal;
use API_TellerRepositories_Model\TellerSession;
use API_TellerRepositories_Model\TellerTransaction;
use API_TellerRepositories_Model\TellerTransfer;

/**
 * Acts as a Data Mapper for the Teller DTOs.
 * It configures the entity/property maps and uses the TContext trait
 * to handle all database interactions and object hydration.
 */
class TellerContext extends Context
{
    // Table name properties specific to this context.
    private string $cashfigure = 'cl_Cashfigures';
    private string $teller = 'cl_Tellers';
    private string $telleraudit  = 'cl_TellerAudits';
    private string $tellercashcount  = 'cl_TellerCashCounts';
    private string $tellerpayment = 'cl_TellerPayments';
    private string $tellerreceipt = 'cl_TellerReceipts';
    private string $tellerreversal = 'cl_TellerReversals';
    private string $tellersession = 'cl_TellerSessions';
    private string $tellertransaction = 'cl_TellerTransactions';
    private string $tellertransfer = 'cl_TellerTransfers';

    /**
     * @inheritDoc
     */
    protected function setEntityMap(): void
    {
        $this->entityMap = [
            'cashfigure' => Cashfigure::class,
            'teller' => Teller::class,
            'telleraudit' => TellerAudit::class,
            'tellercashcount' => TellerCashCount::class,
            'tellerpayment' => TellerPayment::class,
            'tellerreceipt' => TellerReceipt::class,
            'tellerreversal' => TellerReversal::class,
            'tellersession' => TellerSession::class,
            'tellertransaction' => TellerTransaction::class,
            'tellertransfer' => TellerTransfer::class,
            'cashfigurecollection' => CashFigures::class,
            'tellercollection' => Tellers::class,
            'tellerauditcollection' => TellerAudits::class,
            'tellercashcountcollection' => TellerCashCounts::class,
            'tellerpaymentcollection' => TellerPayments::class,
            'tellerreceiptcollection' => TellerReceipts::class,
            'tellerreversalcollection' => TellerReversals::class,
            'tellersessioncollection' => TellerSessions::class,
            'tellertransactioncollection' => TellerTransactions::class,
            'tellertransfercollection' => TellerTransfers::class
        ];
    }

    /**
     * @inheritDoc
     */
    protected function setPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'ProfileID' => 'ProfileId',
            'SessionID' => 'SessionId',
            'TellerID' => 'TellerId',
            'ReferenceID' => 'ReferenceId',
            'AppID' => 'AppId',
            'StockID' => 'StockId',
            'UnitID' => 'UnitId',
            'DiscountID' => 'DiscountId',
            'TaxID' => 'TaxId',
            'TransactionID' => 'TransactionId',
            'PaymentID' => 'PaymentId',
            'RecordID' => 'RecordId'
        ];
    }
}