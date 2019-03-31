<?php

namespace Abiturma\PhpFints\DataElements\Groups;


use Abiturma\PhpFints\DataElements\DataElementGroup;

class SecurityIdentificationDetails extends DataElementGroup
{

    const CLIENT_ROLE = 1;
    
    const PARTY_ID = 0; 
    
    /*
     * DataFields: 
     * 1 Client role (1 = Sender, 2 = Receiver )
     * 2 CID (only if chipcard is used)
     * 3 PartyId/SystemId 
     */

    protected function boot()
    {
        $this->addElement(static::CLIENT_ROLE)->addElement('')->addElement(static::PARTY_ID);
    }

    public function setSystemId($id)
    {
        return $this->setElementAtPosition(3,$id); 
    }
    
    
}