<?php 
/**
 * Global Form for Fees Allocation
 * 
 * @used        fees-allocation / term_bills pages
 * 
 * @return String
 */
function fees_allocation_form($info = null, $class_list = [], $category_listing = [], $clientId = null, $academic_year = null, $academic_term = null, $showButton = true) {
    global $myClass;

    // set the form to display
    $html = '
    <div class="row" id="fees_allocation_wrapper">
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <div style="width:100%" class="d-flex justify-content-between">
                        <div><h4>Allocate Fees To Class</h4></div>
                        <div class="text-right"><i data-toggle="tooltip" title="'.$info.'" class="fa cursor text-primary fa-info"></i></div>
                    </div>
                </div>
                <div class="card-body pt-0 pb-0">
                    <div class="py-3 pt-0" id="fees_allocation_form">
                    
                        <div class="form-group hidden mb-2">
                            <label>Allocate To <span class="required">*</span></label>
                            <select data-width="100%" disabled class="form-control selectpicker" id="allocate_to" name="allocate_to">
                                <option selected value="class">Entire Class</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label>Select Class <span class="required">*</span></label>
                            <select data-width="100%" class="form-control selectpicker" name="class_id">
                                <option value="">Please Select Class</option>';
                                foreach($class_list as $each) {
                                    $html .= "<option data-payment_module='{$each->payment_module}' value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                }
                                $html .= '
                            </select>
                        </div>

                        <div class="form-group mb-2 hidden" id="students_list">
                            <label>Select Student <span class="required">*</span></label>
                            <select data-width="100%" class="form-control selectpicker" name="student_id">
                                <option value="">Please Select Student</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label>Select Category <span class="required">*</span></label>
                            <select disabled data-width="100%" class="form-control selectpicker" name="category_id">
                                <option value="">Please Select Category</option>';
                                foreach($category_listing as $each) {
                                    $html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                }
                            $html .= '
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label>Payment Module <span class="required">*</span></label>
                            <select data-width="100%" disabled class="form-control selectpicker" name="payment_module">
                                <option value="">Please Select Payment Module</option>';
                                foreach(["Monthly", "Termly"] as $period) {
                                    $html .= "<option value=\"{$period}\">{$period}</option>";                            
                                }
                            $html .= '
                            </select>
                        </div>

                        <div class="form-group mb-2 hidden" id="payment_month">
                            <label>Select Month to Assign <span class="required">*</span></label>
                            <select data-width="100%" disabled class="form-control selectpicker" name="payment_month">
                                <option value="">Please Select The Month</option>';
                                foreach(range(0, 11, 1) as $period) {
                                    $month = date('F_Y', strtotime("January +$period month"));
                                    $name = str_ireplace("_", " ", $month);
                                    $html .= "<option value=\"{$month}\">".strtoupper($name)."</option>";                            
                                }
                            $html .= '
                            </select>
                        </div>
                        
                        '.(
                            $showButton ? '
                            <div class="form-group">
                                <label>Set Amount <span class="required">*</span></label>
                                <input type="number" disabled="disabled" name="amount" id="amount" class="form-control">
                                <input type="hidden" disabled="disabled" value="'.$academic_year.'" name="academic_year" id="academic_year" class="form-control">
                                <input type="hidden" disabled="disabled" value="'.$academic_term.'" name="academic_term" id="academic_term" class="form-control">
                            </div>
                                <div class="form-group text-right mb-0" id="allocate_fees_button">
                                <button onclick="return save_Fees_Allocation()" class="btn btn-outline-success"><i class="fa fa-save"></i> Allocate Fee</button></div>' : null
                        ).'

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-6">
            <div class="form-content-loader" style="display: none; position: absolute">
                <div class="offline-content text-center">
                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                </div>
            </div>
            '.$myClass->quick_student_search_form.'
            <table id="simple_load_student" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th width="40%">Name</th>
                        <th>Due</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <td style="background-color: rgba(0,0,0,0.04);" align="center">
                            <input disabled style="height:20px;width:20px;" id="select_all" type="checkbox" class="cursor">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center" colspan="6">Students data appears here.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>';
    return $html;
}

/**
 * Send Fees Payment Reminder to Parent
 * 
 * This modal will be used to send fees arrears and payment reminder to parent/student 
 *
 * @return Array
 **/ 
function fees_payment_reminder_form($student_name, $student_id, $totalOutstanding = 0) {

    // global variable
    global $baseUrl, $myClass;

    // generate the form to popup
    $html = 
        '<div data-backdrop="static" data-keyboard="false" class="modal fade" id="send_Fees_Reminder">
            <form action="'.$baseUrl.'api/fees/send_reminder" method="POST" class="ajax-data-form" id="send_reminder">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Send Fees Payment Reminder</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group font-17">
                                        Send a payment reminder to the guardian of <strong>'.$student_name.'</strong>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Message to Send <span class="required">*</span></label>
                                        <textarea id="student_fees_reminder" maxlength="255" name="message" rows="5" class="form-control"></textarea>
                                        <div class="text-right alert-success p-1"> 
                                            <span class="remaining_count p-1">'.$myClass->sms_text_count.' characters remaining</span>
                                            <span id="messages">0 message</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        '.placeholder_text('student_fees_reminder').'
                                        <div class="border-top pt-2 border-primary">
                                            <label for="description">Do you wish to also send the bill via E-Mail?</label>
                                            <select data-width="100%" class="selectpicker" name="send_via_email" id="send_via_email">
                                                <option value="do_not_send">No! Do not Send Bill</option>
                                                <option value="send">Yes! Send Bill via E-Mail</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer p-0">
                                <input type="hidden" name="student_id[]" value="'.$student_id.'">
                                <input type="hidden" readonly name="total_outstanding['.$student_id.']" value="'.$totalOutstanding.'">
                                <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                                <button data-form_id="send_reminder" type="button-submit" class="btn btn-primary"><i class="fa fa-envelope"></i> Send Reminder</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>';

    return $html;
}