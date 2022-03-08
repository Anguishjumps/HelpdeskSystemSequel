<?php

// Format for ticket
class Ticket
{
    // Properties
    public $ID;
    public $CreatedTimestamp;
    public $TypeID;
    public $SoftwareID;
    public $HardwareID;
    public $ReporterID;
    public $Reporter;
    public $TicketDescription;
    public $TicketPriority;
    public $ResolvedTimestamp;
    public $TicketState;
    public $AssignedSpecialistID;
    public $AssignedSpecialist;
    public $OperatorID;
    public $Operator;
    public $FinalSolutionID;
}



// Format for ticket log
class TicketLog
{
    // Properties
    public $ID;
    public $TicketID;
    public $LogTimestamp;
    public $Text;
    public $LogType;
    public $OriginPersonnelID;
    public $OriginPersonnel;
    public $AssignedPersonnelID;
    public $AssignedPersonnel;
}


// Format for Problem Type
class ProblemType
{
    // Properties
    public $ID;
    public $Problem;
}

// Format for Personnel
class Personnel
{
    // Properties
    public $ID;
    public $FullName;
    public $Job;
    public $Dept;
    public $PhoneNo;
    public $PasswordHash;
    public $Workload;
}

// Format for Software
class Software
{
    // Properties
    public $ID;
    public $SoftwareName;
    public $SoftwareVersion;
    public $LicenseNumber;
    public $PersonID;
}

// Format for Hardware
class Hardware
{
    // Properties
    public $ID;
    public $SerialNo;
    public $Device;
    public $Make;
}

// Format for Specialist
class Specialist
{
    // Properties
    public $PersonID;
    public $Problem;
    public $FullName;
    public $Workload;
}

// Format for Solution
class Solution
{
    // Properties
    public $ID;
    public $ProviderID;
    public $Explanation;
}
