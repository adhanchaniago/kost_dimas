<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guest;
use App\Location;
use App\Invoice;
use App\InvoiceDetail;
use App\RoomDetail;
use Datetime;

class Prorate{
  public $occupants;
  public $roomNumber;
  public $roomType;
  public $occStart;
  public $occEnd;
  public $duration;

  public function __construct($newOccupants, $newRN, $newRT, $newOccS, $newOccE, $newDuration){
    $this->occupants = $newOccupants;
    $this->roomNumber = $newRN;
    $this->roomType = $newRT;
    $this->occStart = $newOccS;
    $this->occEnd = $newOccE;
    $this->duration = $newDuration;
  }
}

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $locations = Location::where('deleted_at',NULL)->get();
        $invoice_details = InvoiceDetail::all();
        $guests = Guest::where('deleted_at',NULL)->get();
        if($guests->isEmpty()){
          return redirect('guests');
        } else {
          return view('Invoices.index')->with('locations',$locations)->with('invoice_details',$invoice_details);
        }
    }

    public function enterSettings(){
        $invoice_detail = InvoiceDetail::orderBy('created_at','desc')->first();
        return view('Invoices.setting')->with('invoice_detail',$invoice_detail);
    }

    public function modifySettings(Request $request){
        $this->validate($request,[
          'vendor_no' => 'required',
          'co_no' => 'required',
          'leg_code' => 'required',
          'bill_to' => 'required',
          'acc_bank' => 'required',
          'acc_num' => 'required',
        ]);
        $invoice_detail = new InvoiceDetail;
        $invoice_detail->vendor_no = $request->input('vendor_no');
        $invoice_detail->co_no = $request->input('co_no');
        $invoice_detail->leg_code = $request->input('leg_code');
        $invoice_detail->bill_to = $request->input('bill_to');
        $invoice_detail->acc_bank = $request->input('acc_bank');
        $invoice_detail->acc_num = $request->input('acc_num');
        $invoice_detail->save();

        return redirect('invoice')->with('success','Invoice Settings Updated');
    }

    public function receipt_index(){
        $invoices = Invoice::all();
        return view('Receipts.index')->with('invoices',$invoices);
    }

    function numberToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

  public function generateInvoice(Request $request){
    $startDate = date_create($request -> input('startDate'));
    if($request -> input('endDate') != null){
      $endDate = date_create($request -> input('endDate'));
    }
    else {
      $endDate = date_create(date('Y-m-d'));
    }

    $locationID = $request -> input('locationID');
    $kertamukti = Location::where('id',$locationID)->where('name','like','%'.'Kertamukti'.'%')->first();
    $kertamukti_ids = Location::where('name','like','%'.'Kertamukti'.'%')->pluck('id')->toArray();
    
    $sum = 0;
    $guests = Guest::where('room_location', $locationID)->where('deleted_at',NULL)->whereNotNull('entry_date')->whereNotNull('room_number')->orderBy('room_number')->get(); 
    if($kertamukti){
      $guests = Guest::where('room_location', $kertamukti_ids[0])->where('deleted_at', NULL)->orWhere('room_location', $kertamukti_ids[1])->where('deleted_at', NULL)->where('deleted_at',NULL)->whereNotNull('entry_date')->whereNotNull('room_number')->orderBy('room_location')->orderBy('room_type')->get();
    }
    $location = Location::where('id', $locationID)->first();

    if($request->input('invoiceDetailID') != null){
      $invoiceDetail = InvoiceDetail::where('id', $request -> input('invoiceDetailID'))->first();
    }
    else{
      $invoiceDetail = InvoiceDetail::orderBy('created_at', 'desc')->first();
    }

    if($request->input('dueDate') != null){
      $dueDate = $request->input('dueDate');
    }
    else{
      $dueDate = date_format($endDate,'Y-m-d');
    }

    $invoiceCode = $location->code.'. / T.'.$this->numberToRoman(date_format($endDate, 'm')).' / '.date_format($endDate, 'm / Y');
    $currentRoomType = RoomDetail::where('room_location', $locationID)->first();
    $i = 0;

    $billMonth = date_format($endDate, 'm');
    $billYear = date_format($endDate, 'Y');
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $billMonth, $billYear);

    $content = '
    <columns column-count="3" vAlign="" column-gap="5" />
      <div>
        <p>
          MAYSA ANJAYA<br>'
          .$location->name.'<br>
          '.$location->address.'
        </p>
      </div>
      <columnbreak />
      <div>
        <p>
          Invoice No<br>
          Date <br>
          Vendor No.<br>
          CO No.<br>
          LEG Approval Code<br>
          Room rental period<br>
          Due Date
        </p>
      </div>
      <columnbreak />
      <div>
        <p>
          : '.$invoiceCode.'<br>
          : '.date('d/m/Y').'<br>
          : '.$invoiceDetail->vendor_no.'<br>
          : '.$invoiceDetail->co_no.'<br>
          : '.$invoiceDetail->leg_code.'<br>
          : '.date_format($startDate,'d').' - '.date_format($endDate, 'd/m/Y').'<br>
          : '.date("d/m/Y",strtotime($dueDate)).'
        </p>
      </div>
      <columns column-count="2" />
      <div>
        <p>
          Bill to:<br>
          '.$invoiceDetail->bill_to.'
        </p>
      </div>
      <columnbreak />
      <div>
      </div>
      <columns column-count="1" />
    ';

    $content .= '<div><table border="1">
      <tr>
          <th>No</th>
          <th>Description</th>
          <th>Qty</th>
          <th>Unit</th>
          <th>Unit Price</th>
          <th>Total Price</th>
      </tr>
      <tr>
          <th>1.</th>
          <th>'.$location->name.'</th>
          <th>'.$location->capacity.'</th>
          <th></th>
          <th></th>
          <th></th>
      </tr>';

    $currentRoom = 1;
    $occupant = null;
    $occStart = null;
    $occEnd = null;
    $prorateArray = array();
    $last = count($guests);

    foreach($guests as $key => $guests){
      $duration = 0;
      if($key == 0){
        $content .= '
              <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th>'.$guests->roomType->room_type.'</th>
                  <th></th>
                  <th></th>
              </tr>';
      }
      if($guests->room_number != $currentRoom){
          if($occupant != null){
            $occupancy = date_diff($occStart, $occEnd)->format("%a") + 1;
            //$totalPrice = $occupancy * $currentRoomType->monthly_rate;
            if($occupancy >= 30){
              $totalPrice = $guests->roomType->monthly_rate;
              $sum += $totalPrice;

              $content .= "<tr>
              <td></td>
              <td>".$currentRoom.".".$occupant."</td>
              <td></td>
              <td></td>
              <td>".number_format($guests->roomType->monthly_rate)."</td>
              <td>".number_format($totalPrice)."</td>
              </tr>";
            }
            else{
              $newProrate = new Prorate($occupant, $currentRoom, $currentRoomType->id, $occStart, $occEnd, $occupancy);

              array_push($prorateArray, $newProrate);
            }
          }
          
          if($guests->room_type != $currentRoomType->id){
            if($guests->room_location != $currentRoomType->room_location){
              $counter = 2;
              $guestBackup = $guests;

              foreach($prorateArray as $prorate){
                  $currentRoomType = RoomDetail::where('id', $prorate->roomType)->first();
                  $totalPrice = $prorate->duration * $currentRoomType->daily_rate;
                  $sum += $totalPrice;

                  if($counter == 2){
                    $content .= "<tr>
                    <td>".$counter.".</td>
                    <td> Prorate ".$prorate->duration." days in room ".$prorate->roomNumber."<br>".$prorate->occupants."</td>
                    <td></td>
                    <td></td>
                    <td>".number_format($currentRoomType->daily_rate)."</td>
                    <td>".number_format($totalPrice)."</td>
                    </tr>";
                    $counter++;
                  }
                  else{
                    $content .= "<tr>
                    <td></td>
                    <td> Prorate ".$prorate->duration." days in room ".$prorate->roomNumber."<br>".$prorate->occupants."</td>
                    <td></td>
                    <td></td>
                    <td>".number_format($currentRoomType->daily_rate)."</td>
                    <td>".number_format($totalPrice)."</td>
                    </tr>";
                  }
              }

              $guests = $guestBackup;
              $prorate = array();
              $location = Location::where('id', $guests->room_location)->first();
              $currentRoomType = RoomDetail::where('id', $guests->room_type)->first();

              $content .= '
                <tr>
                    <th>1.</th>
                    <th>'.$location->name.'</th>
                    <th>'.$location->capacity.'</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>';
            }

            $currentRoomType = RoomDetail::where('id', $guests->room_type)->first();

            $content .= '
              <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th>'.$currentRoomType->room_type.'</th>
                  <th></th>
                  <th></th>
              </tr>';
          }

          $currentRoom = $guests->room_number;
          $occStart = null;
          $occEnd = null;
          $occupant = null;
      }
      
      if($guests->room_number == $currentRoom){
          $guestEntry = date_create($guests->entry_date);

          if($guests->exit_date != null){
            $exitDate = date_create($guests->exit_date);

            if($guestEntry > $startDate){
              $duration = date_diff($startDate, $exitDate);
            }
            else{
              $duration = date_diff($guestEntry, $exitDate);
            }
          }
          else{
            if($guestEntry < $startDate){
              $duration = date_diff($startDate, $endDate);
            }
            else{
              $duration = date_diff($guestEntry, $endDate);
            }
          }

          $duration = $duration->format("%a") + 1;

          if($guestEntry < $startDate || $occStart == $startDate){
            $occStart = $startDate;
          }
          else if ($occStart == null) {
            $occStart = $guestEntry;
          }
          else{
            if($guestEntry < $occStart){
              $occStart = $guestEntry;
            }
          }

          if($guests->exit_date == null){
            $occEnd = $endDate;
          }
          else{
            $occEnd = date_create($guests->exit_date);
          }

          if(date_format($endDate, 'm') == date_format($startDate, 'm')){
            $billMonth = date_format($startDate, 'm');
            $billYear = date_format($startDate, 'Y');
            $daysInMonth = (int)cal_days_in_month(CAL_GREGORIAN, $billMonth, $billYear);

              if($occupant == null){
                $occupant = $guests->name;
              }
              else{
                $occupant .= '/'.$guests->name;
              }
          }
          else{
              if($occupant == null){
                $occupant = $guests->name;
              }
              else{
                $occupant .= '/'.$guests->name;
              }
          }
      }

      if(++$i == $last){
        if($occupant != null){
          $occupancy = date_diff($occStart, $occEnd)->format("%a") + 1;
          //$totalPrice = $occupancy * $currentRoomType->monthly_rate;
          if($occupancy >= 30){
            $totalPrice = $currentRoomType->monthly_rate;
            $sum += $totalPrice;

            $content .= "<tr>
            <td></td>
            <td>".$currentRoom.".".$occupant."</td>
            <td></td>
            <td></td>
            <td>".number_format($currentRoomType->monthly_rate)."</td>
            <td>".number_format($totalPrice)."</td>
            </tr>";
          }
          else{
            $newProrate = new Prorate($occupant, $currentRoom, $currentRoomType->id, $occStart, $occEnd, $occupancy);

            array_push($prorateArray, $newProrate);
          }
        }

          $currentRoom += $guests->room_number;
          $occStart = null;
          $occEnd = null;
          $occupant = null;
      }

    }

    $counter = 2;
    foreach($prorateArray as $prorate){
        echo $prorate->roomType;
        $currentRoomType = RoomDetail::where('id', $prorate->roomType)->first();
        $totalPrice = $prorate->duration * $currentRoomType->daily_rate;
        $sum += $totalPrice;

        if($counter == 2){
          $content .= "<tr>
          <td>".$counter.".</td>
          <td> Prorate ".$prorate->duration." days in room ".$prorate->roomNumber."<br>".$prorate->occupants."</td>
          <td></td>
          <td></td>
          <td>".number_format($currentRoomType->daily_rate)."</td>
          <td>".number_format($totalPrice)."</td>
          </tr>";
          $counter++;
        }
        else{
          $content .= "<tr>
          <td></td>
          <td> Prorate ".$prorate->duration." days in room ".$prorate->roomNumber."<br>".$prorate->occupants."</td>
          <td></td>
          <td></td>
          <td>".number_format($currentRoomType->daily_rate)."</td>
          <td>".number_format($totalPrice)."</td>
          </tr>";
        }
    }

    $content .= "
    <tr>
      <td></td>
      <td>Total:</td>
      <td></td>
      <td></td>
      <td></td>
      <td>".number_format($sum)."</td>
    </tr></table></div>
    <div>
      <columns column-count='2' />
      <div>
        <p>
          Please remit payment in full amount to our bank:<br>
          ".$invoiceDetail->acc_bank.", Account Number: ".$invoiceDetail->acc_num."<br>
          Account Holder: Djoni Muhammad SH.MM<br>
          Bank Address: KCP. Cirendeu
        </p>
      </div>
      <columnbreak />
      <div>
        <p style='padding-bottom: 3cm;text-align: center;'>
          Tangerang, ".date_format($endDate, 'd/m/Y')."
        </p>
        <p style='text-align: center;'>
          Djoni Muhammad
        <p>
      </div>
    </div>";

    $newInvoice = new Invoice;
    $newInvoice->invoiceNumber = $invoiceCode;
    $newInvoice->invoiceDetailID = $invoiceDetail->id;
    $newInvoice->totalBill = $sum;
    $newInvoice->startDate = $startDate;
    $newInvoice->endDate = $endDate;
    $newInvoice->dueDate = $dueDate;
    $newInvoice->room_location = $locationID;
    $newInvoice->save();

    $mpdf = new \Mpdf\Mpdf([
    'default_font' => 'times'
    ]);

    $mpdf->WriteHTML($content);

    $filename = $location->name."_".date_format($endDate, "Y-m-d")." Invoice.pdf";
    $mpdf->Output($filename, 'D');
  }
  //new invoice function
  public function gen_Invoice(Request $request){
    $startDate = date_create($request -> input('startDate'));
    if($request -> input('endDate') != null){
      $endDate = date_create($request -> input('endDate'));
    }
    else {
      $endDate = date_create(date('Y-m-d'));
    }

    $prorates = [];
    $fulls = [];

    $locationID = $request -> input('locationID');
    $kertamukti = Location::where('id',$locationID)->where('name','like','%'.'Kertamukti'.'%')->first();
    $kertamukti_ids = Location::where('name','like','%'.'Kertamukti'.'%')->pluck('id')->toArray();
    
    $sum = 0;
    $guests = Guest::where('room_location', $locationID)->where('deleted_at',NULL)->whereNotNull('entry_date')->whereNotNull('room_number')->orderBy('room_number')->get();
    
    if($kertamukti){
      $guests = Guest::where('room_location', $kertamukti_ids[0])->where('deleted_at', NULL)->orWhere('room_location', $kertamukti_ids[1])->where('deleted_at', NULL)->where('deleted_at',NULL)->whereNotNull('entry_date')->whereNotNull('room_number')->orderBy('room_location')->orderBy('room_type')->get();
    }
    $location = Location::where('id', $locationID)->first();

    if($request->input('invoiceDetailID') != null){
      $invoiceDetail = InvoiceDetail::where('id', $request -> input('invoiceDetailID'))->first();
    }
    else{
      $invoiceDetail = InvoiceDetail::orderBy('created_at', 'desc')->first();
    }

    if($request->input('dueDate') != null){
      $dueDate = $request->input('dueDate');
    }
    else{
      $dueDate = date_format($endDate,'Y-m-d');
    }

    $invoiceCode = $location->code.'. / T.'.$this->numberToRoman(date_format($endDate, 'm')).' / '.date_format($endDate, 'm / Y');
    $currentRoomType = RoomDetail::where('room_location', $locationID)->first();
    $i = 0;

    $billMonth = date_format($endDate, 'm');
    $billYear = date_format($endDate, 'Y');
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $billMonth, $billYear);

    $content = '
    <columns column-count="3" vAlign="" column-gap="5" />
      <div>
        <p>
          MAYSA ANJAYA<br>'
          .$location->name.'<br>
          '.$location->address.'
        </p>
      </div>
      <columnbreak />
      <div>
        <p>
          Invoice No<br>
          Date <br>
          Vendor No.<br>
          CO No.<br>
          LEG Approval Code<br>
          Room rental period<br>
          Due Date
        </p>
      </div>
      <columnbreak />
      <div>
        <p>
          : '.$invoiceCode.'<br>
          : '.date('d/m/Y').'<br>
          : '.$invoiceDetail->vendor_no.'<br>
          : '.$invoiceDetail->co_no.'<br>
          : '.$invoiceDetail->leg_code.'<br>
          : '.date_format($startDate,'d').' - '.date_format($endDate, 'd/m/Y').'<br>
          : '.date("d/m/Y",strtotime($dueDate)).'
        </p>
      </div>
      <columns column-count="2" />
      <div>
        <p>
          Bill to:<br>
          '.$invoiceDetail->bill_to.'
        </p>
      </div>
      <columnbreak />
      <div>
      </div>
      <columns column-count="1" />
    ';

    $content .= '<div><table border="1">
      <tr>
          <th>No</th>
          <th>Description</th>
          <th>Qty</th>
          <th>Unit</th>
          <th>Unit Price</th>
          <th>Total Price</th>
      </tr>
      <tr>
          <th>1.</th>
          <th>'.$location->name.'</th>
          <th>'.$location->capacity.'</th>
          <th></th>
          <th></th>
          <th></th>
      </tr>';

    $currentRoom = 1;
    $occupant = null;
    $occStart = null;
    $occEnd = null;
    $prorateArray = array();
    $last = count($guests);
    $same_rooms = [];
    $prorate_same_rooms = [];

    foreach($guests as $key => $guest){
      $test = $guest->entry_date;
      $occ_start = new DateTime($guest->entry_date);
      $occ_end = $endDate;
      $occupancy = date_diff($occ_start,$occ_end)->format("%a") + 1;
      $currentRoom = $guest->room_number;

      //if stayed more than 30 days, insert guest in same room numbers
      if($occupancy >= 30){
        // $totalPrice = $guest->roomType->monthly_rate;
        // $sum += $totalPrice;
        
        if(sizeof($same_rooms) == 0){
          array_push($same_rooms,$guest);
        }

        if(isset($guests[$key+1]) && $guests[$key+1]->room_number == $currentRoom){
          //$same_rooms .= "/".$guests[$key+1]->name;
          array_push($same_rooms, $guests[$key+1]);
        } else {
          array_push($fulls, $same_rooms);
          $same_rooms = [];
        }
        // $content .= "<tr>
        // <td></td>
        // <td>".$currentRoom.".".$guest->name."</td>
        // <td></td>
        // <td></td>
        // <td>".number_format($guest->roomType->monthly_rate)."</td>
        // <td>".number_format($totalPrice)."</td>
        // </tr>";
        
      } else { //if stayed less than 30 days
        
        if(sizeof($prorate_same_rooms == 0)){
          array_push($prorate_same_rooms, $guest);  
        }

        if(isset($guests[$key+1]) && $guests[$key+1]->room_number == $currentRoom){
          //$same_rooms .= "/".$guests[$key+1]->name;
          array_push($prorate_same_rooms, $guests[$key+1]);
        } else {
          array_push($prorates, $prorate_same_rooms);
          $prorate_same_rooms = [];
        }

        // array_push($prorates, $guest);
      }

    }
    
    for($x = 0;$x < sizeof($fulls);$x++){
      $totalPrice = $fulls[$x][0]->roomType->monthly_rate;
      $sum += $totalPrice;
      $room_guests = "";
      
      for($y=0;$y<sizeof($fulls[$x]);$y++){
        if($room_guests == ""){
          $room_guests = $fulls[$x][$y]->name;
          $content .= "<tr>
          <td></td>
          <td></td>
          <td></td>
          <td>".$fulls[$x][0]->roomType->room_type."</td>
          <td></td>
          <td></td>
          </tr>";
        } else {
          $room_guests .= "/".$fulls[$x][$y]->name;
        }
      }
      $content .= "<tr>
        <td></td>
        <td>".$fulls[$x][0]->room_number.".".$room_guests."</td>
        <td></td>
        <td></td>
        <td>".number_format($fulls[$x][0]->roomType->monthly_rate)."</td>
        <td>".number_format($totalPrice)."</td>
        </tr>";
    }

    //print prorate
    for($x = 0;$x < sizeof($prorates); $x++){
      $occ_start = new DateTime($prorates[$x][0]->entry_date);
      $occ_end = $endDate;
      $occupancy = date_diff($occ_start,$occ_end)->format("%a") + 1;
      $totalPrice = $prorates[$x][0]->roomType->daily_rate * $occupancy;
      $sum += $totalPrice;
      
      for($y = 0;$y < sizeof($prorates[$x]);$y++){

        if($x == 0){
          $content .= "<tr>
          <td>2.</td>
          <td> Prorate ".$occupancy." days in room ".$prorates[$x][$y]->room_number."<br>".$prorates[$x][$y]->name."</td>
          <td></td>
          <td></td>
          <td>".number_format($prorates[$x][$y]->roomType->daily_rate)."</td>
          <td>".number_format($totalPrice)."</td>
          </tr>";
          continue;
        }

        $content .= "<tr>
          <td></td>
          <td> Prorate ".$occupancy." days in room ".$prorates[$x][$y]->room_number."<br>".$prorates[$x][$y]->name."</td>
          <td></td>
          <td></td>
          <td>".number_format($prorates[$x][$y]->roomType->daily_rate)."</td>
          <td>".number_format($totalPrice)."</td>
          </tr>";
      }
      
    }

    $content .= "
    <tr>
      <td></td>
      <td>Total:</td>
      <td></td>
      <td></td>
      <td></td>
      <td>".number_format($sum)."</td>
    </tr></table></div>
    <div>
      <columns column-count='2' />
      <div>
        <p>
          Please remit payment in full amount to our bank:<br>
          ".$invoiceDetail->acc_bank.", Account Number: ".$invoiceDetail->acc_num."<br>
          Account Holder: Djoni Muhammad SH.MM<br>
          Bank Address: KCP. Cirendeu
        </p>
      </div>
      <columnbreak />
      <div>
        <p style='padding-bottom: 3cm;text-align: center;'>
          Tangerang, ".date_format($endDate, 'd/m/Y')."
        </p>
        <p style='text-align: center;'>
          Djoni Muhammad
        <p>
      </div>
    </div>";

    $newInvoice = new Invoice;
    $newInvoice->invoiceNumber = $invoiceCode;
    $newInvoice->invoiceDetailID = $invoiceDetail->id;
    $newInvoice->totalBill = $sum;
    $newInvoice->startDate = $startDate;
    $newInvoice->endDate = $endDate;
    $newInvoice->dueDate = $dueDate;
    $newInvoice->room_location = $locationID;
    $newInvoice->save();

    $mpdf = new \Mpdf\Mpdf([
    'default_font' => 'times'
    ]);

    $mpdf->WriteHTML($content);

    $filename = $location->name."_".date_format($endDate, "Y-m-d")." Invoice.pdf";
    $mpdf->Output($filename, 'D');
  }

  public function generateReceipt(Request $request){
    $invoice = Invoice::where('id', $request->input('invoiceID'))->first();
    $invoiceDetail = InvoiceDetail::where('id', $invoice->invoiceDetailID)->first();
    $location = Location::where('id',$invoice->room_location)->first();
    $endDate = new Datetime($invoice->endDate);
    $invoiceCode = $location->code.'. / T.'.$this->numberToRoman(date_format($endDate, 'm')).'';
    $content =
    '<div>
      <p style="text-align: center;">
        RECEIPT
      </p>
      <p>
        <columns column-count="3" vAlign="" column-gap="5" />
        <columnbreak />
        <columnbreak />
        No. Receipt: '.$invoiceCode.'<br>
        '.date("d/m/Y",strtotime($invoice->dueDate)).'
      </p>
    </div>
    <div>
      <columns column-count="0" vAlign="" column-gap="5" />
      Receipt From: '.$invoiceDetail->bill_to.'<br>
      Said Amount : '.number_format($invoice->totalBill).'
    </div>
    <div>
      <columns column-count="2" />
      <p>
        Being payment of Invoice No.<br>
        Date<br>
        Vendor No.<br>
        CO No.<br>
        LEG Approval Code<BR>
      </p>
      <columnbreak />
      <p>
        : '.$invoice->invoiceNumber.'<br>
        : '.date("d/m/Y",strtotime($invoice->dueDate)).'<br>
        : '.$invoiceDetail->vendor_no.'<br>
        : '.$invoiceDetail->co_no.'<br>
        : '.$invoiceDetail->leg_code.'
      </p>
    </div>
    <div>
      <columns column-count="2" />
        <p>
          Facilities rent room rental periode '.date("d/m/Y",strtotime($invoice->startDate)).' to '.date("d/m/Y",strtotime($invoice->endDate)).'
        </p>
      <columnbreak />
        <p style="padding-bottom: 3cm;text-align: center;">
          Tangerang, '.date("d/m/Y",strtotime($invoice->dueDate)).'
        </p>
        <p style="text-align: center;">
          Djoni Muhammad
        <p>
    </div>';

    $mpdf = new \Mpdf\Mpdf([
    'default_font' => 'times'
    ]);

    $mpdf->WriteHTML($content);

    $filename = $invoice->dueDate." Receipt.pdf";
    $mpdf->Output($filename, 'D');
  }
}
?>
