import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { FormBuilder, FormGroup } from "@angular/forms";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { ORIENTATIONGROUP_INITIAL_STATE } from "../../store/orientationgroup/orientationgroup.initial-state";
import { IOrientationGroupRecords } from "../../store/orientationgroup/orientationgroup.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "orientation-group-select",
    template: `
    <div id="OrientationGroupNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header {{modalHeader | async}}">
              <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>{{ modalText | async }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>
    
    <h4>  Επιλογή Ομάδας Προσανατολισμού  </h4>
    
     <p style="margin-top: 20px; line-height: 2em;"> Παρακαλώ επιλέξτε την ομάδα προσανατολισμού στην οποία θα φοιτήσει ο μαθητής το νέο σχολικό έτος. Έπειτα επιλέξτε <i>Συνέχεια</i>.</p>           
         <ul class="list-group main-view">
            <div *ngFor="let orientgroup$ of OrientationGroup$| async; let i=index; let isOdd=odd; let isEven=even">
                <li class="list-group-item  isclickable" (click)="saveSelected(i)" [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="activechoice === i">
                    <h5>{{orientgroup$.name}}</h5>
                </li>
            </div>
            </ul>



        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-left" (click)="router.navigate(['/epal-class-select']);" >
          <i class="fa fa-backward"></i>
            </button>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="navigateNext()" >
                <span style="font-size: 0.9em; font-weight: bold;">Συνέχεια&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
        </div>
        
`

})
@Injectable() export default class OrientationGroup implements OnInit, OnDestroy {
    private OrientationGroup$: BehaviorSubject<IOrientationGroupRecords>;
    private OrientationGroupSub: Subscription;
    private formGroup: FormGroup;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    private activechoice = <number>-1;

    

    constructor(private fb: FormBuilder,
                private _ogs: OrientationGroupActions,
                private _ngRedux: NgRedux<IAppState>,
                private router: Router) {
        this.OrientationGroup$ = new BehaviorSubject(ORIENTATIONGROUP_INITIAL_STATE);
        this.formGroup = this.fb.group({
                            name: []
                                });

        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.isModalShown = new BehaviorSubject(false);
    };

    ngOnInit() {
     
        let classid = 2;
        let typeid = "ΟΠ";
        (<any>$("#OrientationGroupNotice")).appendTo("body");
        this._ogs.getOrientationGroups(false,classid,typeid);
        
        this.OrientationGroupSub = this._ngRedux.select("orientationGroup")
            .map(orientationGroup => <IOrientationGroupRecords>orientationGroup)
            .subscribe(ogs => {
                   
                        ogs.reduce(({}, orientationgroup) => {
                          if (orientationgroup.get("selected") === true)
                          {
                              this.activechoice = orientationgroup.get("id") -1;
                          }
                              return orientationgroup;


                        }, {});
                                   this.OrientationGroup$.next(ogs);
                console.log("7");
            }, error => { console.log("error selecting orientation"); });

    }

    ngOnDestroy() {
        (<any>$("#OrientationGroupNotice")).remove();
        if (this.OrientationGroupSub) this.OrientationGroupSub.unsubscribe();
    }

    public showModal(): void {
        (<any>$("#OrientationGroupNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#OrientationGroupNotice")).modal("hide");
    }

    public onHidden(): void {
        this.isModalShown.next(false);
    }

    navigateNext() {
        
        this.router.navigate(["/electivecourse-fields-select"]);
    }

    private saveSelected(ind: number): void {
    
        if (this.activechoice == ind)
            return;

        this._ogs.saveOrientationGroupSelected(this.activechoice, ind);
        this.activechoice = ind;
       

        
    }




}
