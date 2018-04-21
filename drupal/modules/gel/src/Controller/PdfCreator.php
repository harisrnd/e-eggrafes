<?php

namespace Drupal\gel\Controller;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\TypedData\Plugin\DataType\TimeStamp;
use Drupal\Core\Language\LanguageManagerInterface;

use FPDF;
use Drupal\gel\Crypt;

define("ERROR_DECODΕ", -1);

class PDFCreator extends ControllerBase {

	protected $entity_query;
	protected $entityTypeManager;
	protected $logger;
	protected $connection;
	protected $pdf;
	protected $fontLight;
	protected $fontBold;
	protected $fontSizeHeader;
	protected $fontSizeRegular;
	protected $crypt;
	protected $webServiceEnabled;

	public function __construct(
		EntityTypeManagerInterface $entityTypeManager,
		QueryFactory $entity_query,
		Connection $connection,
		LoggerChannelFactoryInterface $loggerChannel)
	{
		$this->entityTypeManager = $entityTypeManager;
		$this->entity_query = $entity_query;
		$connection = Database::getConnection();
		$this->connection = $connection;
		$this->logger = $loggerChannel->get('gel');
    }

	public static function create(ContainerInterface $container)
    {
        return new static(
			$container->get('entity_type.manager'),
			$container->get('entity.query'),
			$container->get('database'),
			$container->get('logger.factory')
      );
    }

	public function createApplicantPDF(Request $request, $studentId, $status) {

		try {
			 if (!$request->isMethod('GET')) {
				return $this->respondWithStatus([
					"message" => t("Method Not Allowed")
				], Response::HTTP_METHOD_NOT_ALLOWED);
			 }

			 //user validation

			 $authToken = $request->headers->get('PHP_AUTH_USER');
			 $users = $this->entityTypeManager->getStorage('user')->loadByProperties(array('name' => $authToken));
			 $user = reset($users);
			 if (!$user) {
					 return $this->respondWithStatus([
									 'message' => t("User not found"),
							 ], Response::HTTP_FORBIDDEN);
			 }

			 //Gel-user validation

			 $authToken = $request->headers->get('PHP_AUTH_USER');
			 $gelUsers = $this->entityTypeManager->getStorage('applicant_users')->loadByProperties(array('authtoken' => $authToken));
			 $gelUser = reset($gelUsers);
			 if ($gelUser) {
					 $userid = $gelUser->id();
					 $gelStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('gel_userid' => $userid));
					   if (!$gelStudents) {
							 return $this->respondWithStatus([
											'message' => t("EPAL User not found"),
									], Response::HTTP_FORBIDDEN);
						 }
			 }

			 //user role validation

			 $roles = $user->getRoles();
			 $validRole = false;
			 foreach ($roles as $role)
				 if ($role === "applicant") {
					 $validRole = true;
					 break;
				 }

			 if (!$validRole) {
					 return $this->respondWithStatus([
									 'message' => t("User Invalid Role"),
							 ], Response::HTTP_FORBIDDEN);
			 }

			 $gelStudents = $this->entityTypeManager->getStorage('gel_student')->loadByProperties(array('id'=> $studentId));
			  if (sizeof($gelStudents) === 1) {
						$gelStudent = reset($gelStudents);

						$config_storage = $this->entityTypeManager->getStorage('eggrafes_config');
						$eggrafesConfigs = $config_storage->loadByProperties(array('name' => 'eggrafes_config_gel'));
						$eggrafesConfig = reset($eggrafesConfigs);
						if (!$eggrafesConfig) {
							 return $this->respondWithStatus([
											 'message' => t("eggrafesConfig Enity not found"),
									 ], Response::HTTP_FORBIDDEN);
						}
						else {
							 //$this->applicantsResultsDisabled = $eggrafesConfig->lock_results->getString();
							 $this->webServiceEnabled = $eggrafesConfig->ws_ident->getString();

						}
				}
				else {
					return $this->respondWithStatus([
						"message" => t("No such a studentId Or double studentId")
					], Response::HTTP_INTERNAL_SERVER_ERROR);
				}

			 $this->fontLight = "Ubuntu-Light";
			 $this->fontBold = "Ubuntu-Bold";
			 $this->fontSizeHeader = 14;
			 $this->fontSizeRegular = 11;

			 $this->initPdfHandler();
			 $this->createHeader($gelStudent,$status);
			 $ret = $this->createGuardianInfo($gelStudent);
			 if ($ret === ERROR_DECODΕ)
				 return $this->respondWithStatus([
	 				"message" => t("An unexpected error occured during DECODING data in createGuardianInfo Method ")
	 			], Response::HTTP_INTERNAL_SERVER_ERROR);
			 $ret = $this->createStudentInfo($gelStudent);
			 if ($ret === ERROR_DECODΕ)
				 return $this->respondWithStatus([
	 				"message" => t("An unexpected error occured during DECODING data in createStudentInfo Method ")
	 			], Response::HTTP_INTERNAL_SERVER_ERROR);
			 $this->createStudentChoices($gelStudent);

			 $s = $this->pdf->Output("S", "export.pdf", true);

			 $response = new Response($s, Response::HTTP_OK, ['Content-Type', 'application/pdf']);
			 return $response;
		} //end try
		catch (\Exception $e) {
			$this->logger->warning($e->getMessage());
			return $this->respondWithStatus([
				"message" => t("An unexpected problem occured during createApplicantPDF Method ")
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}

	}



	private function initPdfHandler()	{

		$this->pdf = new FPDF();
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage();

		$this->pdf->AddFont($this->fontLight, '', 'Ubuntu-Light.php');
		$this->pdf->AddFont($this->fontBold, '', 'Ubuntu-Bold.php');

		$this->crypt = new Crypt();

	}

	private function createHeader($student,$status)	{

		$this->pdf->SetFont($this->fontBold, '', 16);
		$this->pdf->MultiCell(0, 8, $this->prepareString('Ηλεκτρονική Δήλωση Προτίμησης ΓΕΛ'), 0, 'C');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeHeader);
		$this->pdf->MultiCell(0, 8, $this->prepareString('με αριθμό δήλωσης: ' . $student->id->value . ' / ' .  date('d-m-y (ώρα: H:i:s)',  $student->changed->value)), 0, 'C');

		$this->pdf->SetFont($this->fontLight, '', 11);
		//if ($this->applicantsResultsDisabled === "1")
		if ($status === "0" ||  $status === "3" || $status === "4")
			$this->pdf->MultiCell(0, 8, $this->prepareString('(Αρχική)'), 0, 'R');

		$this->pdf->Ln();

	}

	private function createGuardianInfo($student)	{

		$width = 45;
		$height = 8;

		try  {
			$guardian_name_decoded = $this->crypt->decrypt($student->guardian_name->value);
			$guardian_surname_decoded = $this->crypt->decrypt($student->guardian_surname->value);
			$guardian_fathername_decoded = $this->crypt->decrypt($student->guardian_fathername->value);
			$guardian_mothername_decoded = $this->crypt->decrypt($student->guardian_mothername->value);
		}
		catch (\Exception $e) {
				$this->logger->warning($e->getMessage());
				return ERROR_DECODΕ;
		}

		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeHeader);
		$this->pdf->SetFillColor(255,178,102);
		$this->pdf->MultiCell(0, $height, $this->prepareString('Στοιχεία Αιτούμενου'), 0, 'C',true);
		$this->pdf->Ln(4);

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString('Όνομα:'), 0, 'L');
		$x=$this->pdf->GetX(); $y=$this->pdf->GetY();
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($guardian_name_decoded), 0, 'L');
		$x_col1=$this->pdf->GetX();$y_col1=$this->pdf->GetY();

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->SetXY($x+$width,$y);
		$this->pdf->Cell($width, $height, $this->prepareString('Επώνυμο:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($guardian_surname_decoded), 0, 'L');
		$x_col2=$this->pdf->GetX();;$y_col2=$this->pdf->GetY();

		$x = ($y_col1 > $y_col2) ? $x_col1 : $x_col2;
		$y = ($y_col1 > $y_col2) ? $y_col1 : $y_col2;
		$this->pdf->SetXY($x,$y);

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString('Όνομα πατέρα:'), 0, 'L');
		$x=$this->pdf->GetX(); $y=$this->pdf->GetY();
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($guardian_fathername_decoded), 0, 'L');
		$x_col1=$this->pdf->GetX();$y_col1=$this->pdf->GetY();

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->SetXY($x+$width,$y);
		$this->pdf->Cell($width, $height, $this->prepareString('Όνομα μητέρας:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($guardian_mothername_decoded), 0, 'L');
		$x_col2=$this->pdf->GetX();;$y_col2=$this->pdf->GetY();

		$x = ($y_col1 > $y_col2) ? $x_col1 : $x_col2;
		$y = ($y_col1 > $y_col2) ? $y_col1 : $y_col2;
		$this->pdf->SetXY($x,$y);
	}

	private function createStudentInfo($student)	{

		$width = 45;
		$height = 8;
		$heightln = 4;

		try  {
			$name_decoded = $this->crypt->decrypt($student->name->value);
			$studentsurname_decoded = $this->crypt->decrypt($student->studentsurname->value);
			$fatherfirstname_decoded = $this->crypt->decrypt($student->fatherfirstname->value);
			$motherfirstname_decoded = $this->crypt->decrypt($student->motherfirstname->value);
			$telnum_decoded = $this->crypt->decrypt($student->telnum->value);
			$regionaddress_decoded = $this->crypt->decrypt($student->regionaddress->value);
			$regiontk_decoded = $this->crypt->decrypt($student->regiontk->value);
			$regionarea_decoded = $this->crypt->decrypt($student->regionarea->value);
			if ( !empty($student->am)>0 ){
				$am_decoded=$this->crypt->decrypt($student->am->value);
			}
			else{
				$am_decoded='';
			}
		}
		catch (\Exception $e) {
				$this->logger->warning("kostas");
				$this->logger->warning($e->getMessage());
				return ERROR_DECODΕ;
		}

		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeHeader);
		$this->pdf->SetFillColor(255,178,102);
		$this->pdf->MultiCell(0, $height, $this->prepareString('Στοιχεία Φοίτησης Μαθητή'), 0, 'C',true);
		$this->pdf->Ln(4);

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width+15, $height, $this->prepareString('Σχολείο τελευταίας φοίτησης:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell(0, $height, $this->prepareString($student->lastschool_schoolname->value), 0, 'L');

		if ( $am_decoded==="" || $this->webServiceEnabled==="0" ){
			$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
			$this->pdf->Cell($width+15, $height, $this->prepareString('Τάξη τελευταίας φοίτησης:'), 0, 'L');
			$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
			$this->pdf->Cell($width, $height, $this->prepareString($this->retrieveClassName($student->lastschool_class->value)), 0, 'L');
			$this->pdf->Ln();
		}

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width+15, $height, $this->prepareString('Σχ.έτος τελευταίας φοίτησης:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString($student->lastschool_schoolyear->value), 0, 'L');
		$this->pdf->Ln();

		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeHeader);
		$this->pdf->SetFillColor(255,178,102);
		$this->pdf->MultiCell(0, $height, $this->prepareString('Προσωπικά Στοιχεία μαθητή'), 0, 'C',true);
		$this->pdf->Ln(4);

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString('Όνομα μαθητή:'), 0, 'L');
		$x=$this->pdf->GetX(); $y=$this->pdf->GetY();
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($name_decoded), 0, 'L');
		$x_col1=$this->pdf->GetX();$y_col1=$this->pdf->GetY();

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->SetXY($x+$width,$y);
		$this->pdf->Cell($width, $height, $this->prepareString('Επώνυμο μαθητή:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($studentsurname_decoded), 0, 'L');
		$x_col2=$this->pdf->GetX();;$y_col2=$this->pdf->GetY();

		$x = ($y_col1 > $y_col2) ? $x_col1 : $x_col2;
		$y = ($y_col1 > $y_col2) ? $y_col1 : $y_col2;
		$this->pdf->SetXY($x,$y);

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString('Όνομα πατέρα:'), 0, 'L');
		$x=$this->pdf->GetX(); $y=$this->pdf->GetY();
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($fatherfirstname_decoded), 0, 'L');
		$x_col1=$this->pdf->GetX();$y_col1=$this->pdf->GetY();

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->SetXY($x+$width,$y);
		$this->pdf->Cell($width, $height, $this->prepareString('Όνομα μητέρας:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($motherfirstname_decoded), 0, 'L');
		$x_col2=$this->pdf->GetX();;$y_col2=$this->pdf->GetY();

		$x = ($y_col1 > $y_col2) ? $x_col1 : $x_col2;
		$y = ($y_col1 > $y_col2) ? $y_col1 : $y_col2;
		$this->pdf->SetXY($x,$y);

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString('Ημ/νία γέννησης:'), 0, 'L');
		$x=$this->pdf->GetX(); $y=$this->pdf->GetY();
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString(date("d-m-Y", strtotime($student->birthdate->value))), 0, 'L');
		$x_col1=$this->pdf->GetX();$y_col1=$this->pdf->GetY();

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->SetXY($x+$width,$y);
		if ($am_decoded!==''){
			$this->pdf->Cell($width, $height, $this->prepareString('Αριθμός Μητρώου:'), 0, 'L');
		}
		else{
			$this->pdf->Cell($width, $height, "", 0, 'L');
		}
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($am_decoded), 0, 'L');
		$x_col2=$this->pdf->GetX();;$y_col2=$this->pdf->GetY();

		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeHeader);
		$this->pdf->SetFillColor(255,178,102);
		$this->pdf->MultiCell(0, $height, $this->prepareString('Στοιχεία Επικοινωνίας'), 0, 'C',true);
		$this->pdf->Ln(4);

		if ( $am_decoded==="" || $this->webServiceEnabled==="0" ){

			$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
			$regAddressTxt = 'ΤΚ: ' . $regiontk_decoded . ', ' . $regionarea_decoded;
			$this->pdf->Cell($width, $height, $this->prepareString('Διεύθυνση κατοικίας: '), 0, 'L');
			$x=$this->pdf->GetX(); $y=$this->pdf->GetY();
			$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
			$this->pdf->multiCell($width, $height, $this->prepareString($regionaddress_decoded), 0, 'L');
			$x_col1=$this->pdf->GetX();$y_col1=$this->pdf->GetY();

			$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
			$this->pdf->SetXY($x+$width,$y);
			$this->pdf->Cell($width, $height, $this->prepareString('ΤΚ - Πόλη: '), 0, 'L');
			$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
			$this->pdf->multiCell($width, $height, $this->prepareString($regAddressTxt), 0, 'L');
			$x_col2=$this->pdf->GetX();;$y_col2=$this->pdf->GetY();

			$x = ($y_col1 > $y_col2) ? $x_col1 : $x_col2;
			$y = ($y_col1 > $y_col2) ? $y_col1 : $y_col2;
			$this->pdf->SetXY($x,$y);
		}

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString('Δήλωση από:'), 0, 'L');
		$x=$this->pdf->GetX(); $y=$this->pdf->GetY();
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString($student->relationtostudent->value), 0, 'L');
		$x_col1=$this->pdf->GetX();$y_col1=$this->pdf->GetY();

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->SetXY($x+$width,$y);
		$this->pdf->Cell($width, $height, $this->prepareString('Τηλ. επικ/νίας:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
		$this->pdf->multiCell($width, $height, $this->prepareString($telnum_decoded), 0, 'L');
		$x_col2=$this->pdf->GetX();;$y_col2=$this->pdf->GetY();


	}

	private function createStudentChoices($student)	{

		$width = 55;
		$height = 8;

		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeHeader);
		$this->pdf->SetFillColor(255,178,102);
		$this->pdf->MultiCell(0, $height, $this->prepareString('Επιλογές Μαθητή'), 0, 'C',true);
		$this->pdf->Ln(4);

		$this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
		$this->pdf->Cell($width, $height, $this->prepareString('Τάξη εγγραφής:'), 0, 'L');
		$this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);

        //$this->pdf->Cell($width, $height, $this->prepareString($this->retrieveGelClassName($student->nextclass->entity->get('id')->value )), 0, 'L');
        //both work
        $this->pdf->Cell($width, $height, $this->prepareString($this->retrieveGelClassName($student->nextclass->getString() )), 0, 'L');

		$this->pdf->Ln();

        if ($student->nextclass->getString() === "2" || $student->nextclass->getString() === "3" || $student->nextclass->getString() === "6"|| $student->nextclass->getString() === "7" )
            $this->createOrientationGroupChoice($student);

        if ($student->nextclass->getString() === "1" || $student->nextclass->getString() === "3" || $student->nextclass->getString() === "4" )
            $this->createElectiveCourseChoices($student);
	}




    private function createOrientationGroupChoice($student)	{

        $width = 55;
        $height = 8;
        $this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);

        $OrientGroups = $this->entityTypeManager->getStorage('gel_student_choices')->loadByProperties(array('student_id'=> $student->id->value));
        $this->pdf->Cell($width, $height, $this->prepareString('Ομάδα Προσανατολισμού:'), 0, 'L');
        foreach ($OrientGroups as $OrientGroup){

            if ($OrientGroup->choice_id->entity->get('choicetype')->value === "ΟΠ"){
                $this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
                $this->pdf->Cell($width, $height, $this->prepareString($OrientGroup->choice_id->entity->get('name')->value), 0, 'L');

            }
        }
    }

    private function createElectiveCourseChoices($student)	{

        $width = 55;
        $height = 8;
        $this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);

        $result = \Drupal::entityQuery('gel_student_choices')
		->condition('student_id', $student->id->value)
        ->sort('order_id')
        ->execute();
        $ElectiveCourses = \Drupal::entityTypeManager()->getStorage('gel_student_choices')->loadMultiple($result);

        $this->pdf->Ln();
        $this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
        $this->pdf->Cell(35, $height, $this->prepareString('Μάθημα Επιλογής:'), 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont($this->fontLight, '', $this->fontSizeRegular);
        $this->pdf->Cell(20, $height, $this->prepareString(''), 0, 'L');
        $this->pdf->Cell(50, $height, $this->prepareString('Σειρα Προτιμησης:'), 0, 0, 'C');
        $this->pdf->Cell($width, $height, $this->prepareString('Τιτλος Μαθήματος:'), 0, 'L');

        $this->pdf->Ln();

        foreach ($ElectiveCourses as $ElectiveCourse){

            if ($ElectiveCourse->choice_id->entity->get('choicetype')->value === "ΕΠΙΛΟΓΗ"){
                $this->pdf->SetFont($this->fontBold, '', $this->fontSizeRegular);
                $this->pdf->Cell(20, $height, $this->prepareString(''), 0, 'L');
                $this->pdf->Cell(50, $height, $this->prepareString($ElectiveCourse->order_id->value), 0, 0, 'C');
                $this->pdf->Cell($width, $height, $this->prepareString($ElectiveCourse->choice_id->entity->get('name')->value), 0, 'L');
                $this->pdf->Ln();
            }
        }
    }



    private function retrieveClassName($classId)	{
        if ($classId === "1")
            return 'Α\' τάξη';
        else if ($classId === "2")
            return 'Β\' τάξη';
        else if ($classId === "3")
            return 'Γ\' τάξη';
        else if ($classId === "4")
            return 'Δ\' τάξη';
        else
            return 'Μη διαθέσιμη τάξη';
    }

    private function retrieveGelClassName($classId)	{
			if ($classId === "1")
				return 'Α\' τάξη - Ημερήσιο ΓΕ.Λ.';
			else if ($classId === "2")
				return 'Β\' τάξη - Ημερήσιο ΓΕ.Λ.';
			else if ($classId === "3")
				return 'Γ\' τάξη - Ημερήσιο ΓΕ.Λ.';
			else if ($classId === "4")
                return 'Α\' τάξη - Εσπερινό ΓΕ.Λ.';
            else if ($classId === "5")
                return 'Β\' τάξη - Εσπερινό ΓΕ.Λ.';
            else if ($classId === "6")
                return 'Γ\' τάξη - Εσπερινό ΓΕ.Λ.';
            else if ($classId === "7")
				return 'Δ\' τάξη - Εσπερινό ΓΕ.Λ.';
			else
				return 'Μη διαθέσιμη τάξη';
	}

	private function retrieveAgreementLiteral($aggreeId)	{
		if ($aggreeId === "1")
			return 'ΝΑΙ';
		else
			return 'ΟΧΙ';
	}





	private function prepareString($string, $from_encoding = 'UTF-8', $to_encoding = 'ISO-8859-7') {
		return iconv($from_encoding, $to_encoding, $string);
	}



	private function respondWithStatus($arr, $s) {
		$res = new JsonResponse($arr);
		$res->setStatusCode($s);
		return $res;
	}

}
