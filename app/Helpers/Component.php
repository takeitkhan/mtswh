<?php
namespace App\Helpers;

class Component{

    public static function confirmModal($modal_id, $form_id, $title="", $subtitle, $modal_body="", $submit_button = true){
        if($submit_button == true){
            $submit = '<button type="button" id="modalCloseBtn'.$modal_id.'" class="modal-action modal-cancel" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="modal-action modal-ok" id="modalOkBtn'.$modal_id.'"
            type="button" xonclick="$(\''.$form_id.'\').submit()"">Submit</button>';
        }else {
            $submit = '<button type="button" class="modal-action modal-cancel" data-bs-dismiss="modal">Ok</button>';
        }
        $html = '<div class="modal fade" style="background: #ffffffe6" id="'.$modal_id.'" xtabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data--bs-keyboard="false" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content modal-alert">
                            <div class="modal-body text-center">
                            <h3 class="title mb-2 text-dark">'.$title.'</h3>
                            <p class="subtitle text-dark">'.$subtitle.'</p>
                            <div class="">'.$modal_body.'</div>
                            '.$submit.'
                            </div>
                        </div>
                    </div>
                </div>';
                return $html;
    }


    /**
     *
     * JS Custom Modal
     * */

    public static function jsModal($buttonId, $options = []){
        $default = [
            'modalId' =>  $buttonId.'OpenModal',
            'formId'  => null,
            'modalHeader' => null,
            'modalSubHeader' => null,
            'modalBodyId' => $buttonId.'ModalBody',
            'modalBody' => null,
            'formAction' => null,
            'files' => false,
            'modalSize' => 'lg',
            'saveBtn' => 'Submit',
            'btnWrapperId' => null,
            'submitBtn' => true,
            'modalBg' => ' #ffffffe6',
            'use' => 'id',
        ];
        $arr = array_merge($default, $options);
        $formId = $arr['formId'] ?? 'jsModalForm'.$buttonId;
        ?>
            <?php if($arr['formAction']) :?>
            <form action="<?php echo $arr['formAction'];?>" method="post" id="<?php echo $formId;?>">
            <?php echo csrf_field() ?>
            <?php endif ?>
                <div class="modal fade" style="background: <?php echo $arr['modalBg'];?>" id="<?php echo $arr['modalId'];?>" xtabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" >
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content modal-alert">
                            <div class="modal-body text-center">

                            <h3 class="title mb-2 text-dark"><?php echo $arr['modalHeader'];?></h3>

                            <p class="subtitle text-dark"><?php echo $arr['modalSubHeader'];?></p>

                            <div class=""><?php echo $arr['modalBody'];?></div>

                            <?php if($arr['submitBtn'] == true){ ?>
                                <button type="button" id="modalCloseBtn<?php echo $arr['modalId'];?>" class="modal-action modal-cancel" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="modal-action modal-ok"
                            type="button" xonclick="confirmAlertBeforeSubmit(`<?php echo $formId;?>`)">Submit</button>
                            <?php } else { ?>
                                <button type="button" class="modal-action modal-cancel" data-bs-dismiss="modal">Ok</button>';
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php if($arr['formAction']) :?>
            </form>
            <?php endif ;
            self::modalScriptLoad($buttonId, ['btnWrapperId' => $arr["btnWrapperId"],  'use' => $arr['use'] ]);
    }

    /**
     * bootstrapModal
     * BootStrap Modal
     * @param  mixed $buttonId
     * @param  mixed $options
     * @return void
     */
    public static function bootstrapModal($buttonId, $options = []){
        $default = [
            'modalId' =>  $buttonId.'OpenModal',
            'modalHeader' => null,
            'modalHeaderShow' => true,
            'modalBodyId' => $buttonId.'ModalBody',
            'formAction' => null,
            'files' => false,
            'modalSize' => 'lg',
            'saveBtn' => 'Submit',
            'btnWrapperId' => null,
            'use'   => 'id',
            'position' => null, // can pass only css class left & right
            'backdrop' => false,
            'backshadow' => false,
            'scrollable' => false,
        ];
        $arr = array_merge($default, $options);
        ?>
            <?php if($arr['formAction']) :?>
            <form action="<?php echo $arr['formAction'];?>" method="post">
            <?php echo csrf_field() ?>
            <?php endif ?>
                <div class="modal <?php echo $arr['position'] ?> <?php echo $arr['modalId'];?> fade <?php echo $arr['backshadow'] ? 'modalBackdrop' : null;?> " id="<?php echo $arr['modalId'];?>" xtabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="<?php echo $arr['backdrop'] ? 'static' : null;?>" data-bs-keyboard="false">
                    <div class="modal-dialog <?php echo $arr['scrollable'] ? 'modal-dialog-scrollable' : null; ?> modal-<?php echo $arr['modalSize'];?>">
                        <div class="modal-content">
                            <?php if($arr['modalHeaderShow']) : ?>
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel"><?php echo $arr['modalHeader'];?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            <div class="modal-body" id="<?php echo $arr['modalBodyId'];?>">

                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <?php if($arr['saveBtn']) : ?>
                                <button type="submit" class="btn btn-primary btn-sm"><?php echo $arr['saveBtn'];?></button>
                            <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php if($arr['formAction']) :?>
            </form>
            <?php endif ?>

          <?php
          self::modalScriptLoad($buttonId, ['btnWrapperId' => $arr["btnWrapperId"], 'use' => $arr['use'] ]);
    }

    /**
     * modalScriptLoad
     * Modal Open JS Script
     * @param  mixed $buttonId
     * @param  mixed $options
     * @return void
     */
    public static function modalScriptLoad($buttonId, $options = []){
        $default = [
            'modalId' =>  $buttonId.'OpenModal',
            'btnWrapperId' => null,
            'use'   => 'id',
        ];
        $arr = array_merge($default, $options);
        $use = $arr['use'] == 'id' ? '#' : '.';
         if($arr["btnWrapperId"]): ?>
            <script>
                $('#<?php echo $arr["btnWrapperId"] ;?>').on('click',   '#<?php echo $buttonId ?>', function(e){
                // $(btns).click(modalBtn, function(e){
                    e.preventDefault();
                    $('#<?php echo $arr['modalId'];?>').modal("show");
                })
            </script>
            <?php else :?>
            <script>
                $(document).on('click', '<?php echo $use;?><?php echo $buttonId ?>', function(e){
                // $('<?php //echo $use;?><?php //echo $buttonId ?>').on('click', function(e){
                // $(btns).click(modalBtn, function(e){
                    e.preventDefault();
                    //alert('pk')
                    $('<?php echo $use;?><?php echo $arr['modalId'];?>').modal("show");
                })
            </script>
            <?php endif;

    }


    /**
     * sidebarModal
     * Sidebar Modal
     * @param  mixed $buttonId
     * @param  mixed $options
     * @return void
     */
    public static function sidebarModal($buttonId, $options=[]){
            $default = [
                'modalId' => $buttonId.'OpenModal',
            ];
            $arr = array_merge($default, $options);?>
            <div class="modal left fade" id="<?php echo $arr['modalId'] ?>" xtabindex="" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="nav flex-sm-column flex-row">
                                <a class="nav-item nav-link active" href="#">Home</a>
                                <a href="#" class="nav-item nav-link">Link</a>
                                <a href="#" class="nav-item nav-link">Link</a>
                                <a href="#" class="nav-item nav-link">Link</a>
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        self::modalScriptLoad($buttonId);
    }
}


?>
