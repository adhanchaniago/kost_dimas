<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guest;
use App\Location;
use App\Invoice;
use App\InvoiceDetail;
use App\RoomDetail;

class InvoiceController extends Controller
{
    public function index(){
        $locations = Location::where('deleted_at',NULL)->get();
        $invoice_details = InvoiceDetail::all();
        return view('Invoices.index')->with('locations',$locations)->with('invoice_details',$invoice_details);
    }

    public function enterSettings(){
        $invoice_detail = InvoiceDetail::orderBy('created_at','desc')->first();
        
        return view('Invoices.setting')->with('invoice_detail',$invoice_detail);
    }

    public function modifySettings(Request $request){
        $invoice_detail = new InvoiceDetail;
        $invoice_detail->vendor_no = $request->input('vendor_no');
        $invoice_detail->co_no = $request->input('co_no');
        $invoice_detail->leg_code = $request->input('leg_code');
        $invoice_detail->bill_to = $request->input('bill_to');
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

    //In the case if Kertamukti, append guests from Ketamukti II to the array. On room change, create an if that if location is different,
    //print out pro rate contents, add new header for KM II and continue the loop

    $locationID = $request -> input('locationID');
    $sum = 0;
    $guests = Guest::where('room_location', $locationID)->where('deleted_at',NULL)->orderBy('room_number')->get();
    if($locationID == 1){
      $guests = Guest::where('room_location', '1')->where('deleted_at', NULL)->orWhere('room_location', '2')->where('deleted_at', NULL)->orderBy('room_location')->orderBy('room_number')->get();
    }
    $location = Location::where('id', $locationID)->first();
    $invoiceDetail = InvoiceDetail::where('id', $request -> input('invoiceDetailID'))->first();
    $currentRoomType = RoomDetail::where('id', $locationID)->first();
    $i = 0;

    $content = '
    <columns column-count="3" vAlign="" column-gap="5" />
      <div>
        <p>
          MAYSA ANJAYA<br>'
          .$location->name.'<br>
          ADDRESS HERE
        </p>
      </div>
      <columnbreak />
      <div>
        <p>
          Invoice No<br>
          Date<br>
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
          : '.$location->code.'. / T.'.$this->numberToRoman(date_format($endDate, 'm')).' / '.date_format($endDate, 'm / Y').'<br>
          : '.date('Y-m-d').'<br>
          : '.$invoiceDetail->vendor_no.'<br>
          : '.$invoiceDetail->co_no.'<br>
          : '.$invoiceDetail->leg_code.'<br>
          : '.date_format($startDate,'d').' - '.date_format($endDate, 'd-m-Y').'<br>
          : '.date_format($endDate,'Y-m-d').'
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
          <th>'.$currentRoomType->room_type.'</th>
          <th></th>
          <th></th>
      </tr>';

    $currentRoom = 1;
    $occupant = null;
    $prorate = array();
    $last = count($guests);

    foreach($guests as $guests){
      $duration = 0;

      if($guests->room_number != $currentRoom){
          if($occupant != null){
            $occupancy = date_diff($occStart, $occEnd)->format("%a") + 1;
            $totalPrice = $occupancy * $currentRoomType->monthly_rate;
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

          if($guests->room_type != $currentRoomType->id){
            if($guests->room_location != $currentRoomType->room_location){
              $counter = 2;
              foreach($prorate as $guests){
                $guestEntry = date_create($guests->entry_date);

                if($guests->exit_date != null){
                  $exitDate = date_create($guests->exit_date);

                  if(date_diff($guestEntry, $startDate)->format("%a") <= 0){
                    $duration = date_diff($startDate, $exitDate);
                  }
                  else{
                    $duration = date_diff($guestEntry, $exitDate);
                  }
                }
                else{
                  if(date_diff($guestEntry, $startDate)->format("%a") <= 0){
                    $duration = date_diff($startDate, $endDate);
                  }
                  else{
                    $duration = date_diff($guestEntry, $endDate);
                  }
                }

                $currentRoomType = RoomDetail::where('id', $guests->room_type)->first();
                $totalPrice = $duration->format("%a") * $currentRoomType->daily_rate;
                $sum += $totalPrice;

                if($counter == 2){
                  $content .= "<tr>
                  <td>".$counter.".</td>
                  <td> Prorate ".$duration->format("%a")." days in room ".$guests->room_number."<br>".$guests->name."</td>
                  <td></td>
                  <td></td>
                  <td>".number_format($currentRoomType->daily_rate)."</td>
                  <td>".number_format($totalPrice)."</td>
                  </tr>";
                }
                else{
                  $content .= "<tr>
                  <td></td>
                  <td> Prorate ".$duration->format("%a")." days in room ".$guests->room_number."<br>".$guests->name."</td>
                  <td></td>
                  <td></td>
                  <td>".number_format($currentRoomType->daily_rate)."</td>
                  <td>".number_format($totalPrice)."</td>
                  </tr>";
                }
              }

              $prorate = array();
              $location = Location::where('id', $guests->room_location)->first();
              $currentRoomType = RoomDetail::where('id', $guests->room_type)->first();

              $content .= '
                <tr>
                    <th>1.</th>
                    <th>'.$location->name.'</th>
                    <th>'.$location->capacity.'</th>
                    <th>'.$currentRoomType->room_type.'</th>
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

            if(date_diff($guestEntry, $startDate)->format("%a") >= 0){
              $duration = date_diff($startDate, $exitDate);
            }
            else{
              $duration = date_diff($guestEntry, $exitDate);
            }
          }
          else{
            if(date_diff($guestEntry, $startDate)->format("%a") <= 0){
              $duration = date_diff($startDate, $endDate);
            }
            else{
              $duration = date_diff($guestEntry, $endDate);
            }
          }

          if(date_diff($guestEntry, $startDate)->format("%a") >= 0 || $occStart == $startDate){
            $occStart = $startDate;
          }
          else if ($occStart == null) {
            $occStart = $guestEntry;
          }
          else{
            if(date_diff($guestEntry, $occStart)->format("%a") <= 0){
              $occStart = $guestEntry;
            }
          }

          if($guests->exit_date == null){
            $occEnd = $endDate;
          }
          else{
            $occEnd = date_create($guests->exit_date);
          }

          if($duration->format("%a") >= 30){
            if($occupant == null){
              $occupant = $guests->name;
            }
            else{
              $occupant .= '/'.$guests->name;
            }
          }
          else{
            array_push($prorate, $guests);
          }
      }

      if(++$i == $last){
          if($occupant != null){
            $occupancy = date_diff($occStart, $occEnd)->format("%a") + 1;
            $totalPrice = $occupancy * $currentRoomType->monthly_rate;
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

          $currentRoom += $guests->room_number;
          $occStart = null;
          $occEnd = null;
          $occupant = null;
      }
    }

    $counter = 2;
    foreach($prorate as $guests){
      $guestEntry = date_create($guests->entry_date);

      if($guests->exit_date != null){
        $exitDate = date_create($guests->exit_date);

        if(date_diff($guestEntry, $startDate)->format("%a") <= 0){
          $duration = date_diff($startDate, $exitDate);
        }
        else{
          $duration = date_diff($guestEntry, $exitDate);
        }
      }
      else{
        if(date_diff($guestEntry, $startDate)->format("%a") <= 0){
          $duration = date_diff($startDate, $endDate);
        }
        else{
          $duration = date_diff($guestEntry, $endDate);
        }
      }

      if($exitDate < $startDate){        
        $currentRoomType = RoomDetail::where('id', $guests->room_type)->first();
        $totalPrice = $duration->format("%a") * $currentRoomType->daily_rate;
        $sum += $totalPrice;

        if($counter == 2){
          $content .= "<tr>
          <td>".$counter.".</td>
          <td> Prorate ".$duration->format("%a")." days in room ".$guests->room_number."<br>".$guests->name."</td>
          <td>".date_diff($exitDate, $startDate)->format('%a')."</td>
          <td></td>
          <td>".number_format($currentRoomType->daily_rate)."</td>
          <td>".number_format($totalPrice)."</td>
          </tr>";
          $counter++;
        }
        else{
          $content .= "<tr>
          <td></td>
          <td> Prorate ".$duration->format("%a")." days in room ".$guests->room_number."<br>".$guests->name."</td>
          <td></td>
          <td></td>
          <td>".number_format($currentRoomType->daily_rate)."</td>
          <td>".number_format($totalPrice)."</td>
          </tr>";
        }
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
          Bank Rakyat Indonesia, account number: 0524-01-000179-56-4<br>
          account Holder: Djoni Muhammad SH.MM<br>
          Bank Address: KCP. Cirendeu
        </p>
      </div>
      <columnbreak />
      <div>
        <p style='padding-bottom: 3cm;text-align: center;'>
          Tangerang, ".date_format($endDate, 'Y-m-d')."
        </p>
        <p style='text-align: center;'>
          Djoni Muhammad
        <p>
      </div>
    </div>";

    $newInvoice = new Invoice;
    $newInvoice->invoiceNumber = "Invoice Number";
    $newInvoice->totalBill = $sum;
    $newInvoice->startDate = $startDate;
    $newInvoice->endDate = $endDate;
    $newInvoice->dueDate = $endDate;
    $newInvoice->room_location = $locationID;
    $newInvoice->save();

    $mpdf = new \Mpdf\Mpdf();

    $mpdf->WriteHTML($content);

    $filename = date_format($endDate, "Y-m-d")." Invoice.pdf";
    $mpdf->Output($filename, 'D');
  }

  public function generateReceipt(Request $request){
    $invoice = Invoice::where('id', $request->input('invoiceID'))->first();

    $content =
    '<div>
      <p style="text-align: center;">
        RECEIPT
      </p>
      <p>
        <columns column-count="3" vAlign="" column-gap="5" />
        <columnbreak />
        <columnbreak />
        No. Receipt: RECEIPT NUMBER HERE<br>
        '.$invoice->dueDate.'
      </p>
    </div>
    <div>
      <columns column-count="0" vAlign="" column-gap="5" />
      Receipt From: INTERNATIONAL ORGANIZATION FOR MIGRATION (IOM)<br>
      Said Amount : '.$invoice->totalBill.'
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
        : '.$invoice->dueDate.'<br>
        : VENDOR NUMBER<br>
        : CO NUMBER<br>
        : LEG APPROVAL CODE
      </p>
    </div>
    <div>
      <columns column-count="2" />
        <p>
          Facilities rent room rental periode '.$invoice->startDate.' to '.$invoice->endDate.'
        </p>
      <columnbreak />
        <p style="padding-bottom: 3cm;text-align: center;">
          Tangerang, '.$invoice->dueDate.'
        </p>
        <p style="text-align: center;">
          Djoni Muhammad
        <p>
    </div>';

    $mpdf = new \Mpdf\Mpdf();

    $mpdf->WriteHTML($content);

    $filename = $invoice->dueDate." Receipt.pdf";
    $mpdf->Output($filename, 'D');
  }
}
?>
