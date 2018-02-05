import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { FormBuilder, FormGroup } from "@angular/forms";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { ORIENTATIONGROUP_INITIAL_STATE } from "../../store/orientationgroup/orientationgroup.initial-state";
import { IOrientationGroupRecords } from "../../store/orientationgroup/orientationgroup.types";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
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
    <div class = "loading" *ngIf=" !(OrientationGroup$ | async) || (OrientationGroup$ | async).size === 0"></div>

     <p style="margin-top: 20px; line-height: 2em;"> Παρακαλώ επιλέξτε την ομάδα προσανατολισμού στην οποία θα φοιτήσει ο μαθητής το νέο σχολικό έτος. Έπειτα επιλέξτε <i>Συνέχεια</i>.</p>
     <!--
         <ul class="list-group main-view">
            <div *ngFor="let orientgroup$ of OrientationGroup$| async; let i=index; let isOdd=odd; let isEven=even">
                <li class="list-group-item  isclickable" (click)="saveSelected(i)" [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="activechoice === i">
                    <h5>{{orientgroup$.name}}</h5>
                </li>
            </div>
            </ul>
      -->


      <div class="list-group" *ngFor="let orientgroup$ of OrientationGroup$ | async; let i=index;">
          <button *ngIf = "orientgroup$.selected === true" type="button" class="list-group-item list-group-item-action active" (click)="saveSelected(i,1)" >{{orientgroup$.name}}</button>
          <button *ngIf = "orientgroup$.selected === false" type="button" class="list-group-item list-group-item-action" (click)="saveSelected(i,0)" >{{orientgroup$.name}}</button>
      </div>



        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-left" (click)="router.navigate(['/gel-class-select']);" >
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
    private gelclassesSub: Subscription;
    private formGroup: FormGroup;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    private activeChoice = <number>-1;
    private activeClassId = <number>-1;
    private listsize = <number>0;



    constructor(private fb: FormBuilder,
                private _cfb: GelClassesActions,
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

        (<any>$("#OrientationGroupNotice")).appendTo("body");

        this.selectClass();



    }

    ngOnDestroy() {
        (<any>$("#OrientationGroupNotice")).remove();
        if (this.OrientationGroupSub) this.OrientationGroupSub.unsubscribe();
        if (this.gelclassesSub) this.gelclassesSub.unsubscribe();
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

    selectClass() {
      //this._cfb.getClassesList(false);
      this.gelclassesSub = this._ngRedux.select("gelclasses")
          .map(gelclasses => <IGelClassRecords>gelclasses)
          .subscribe(ecs => {
              this.activeClassId = -1;
              if (ecs.size > 0) {
                   ecs.reduce(({}, gelclass) => {
                      if (gelclass.get("selected")===true ){
                          this.activeClassId = gelclass.get("id");
                          this.getAppropriateOrientations();
                      }
                      return gelclass;
                  }, {});
              }
          }, error => { console.log("error selecting gelclasses"); });
    }

    getAppropriateOrientations()  {
      this._ogs.getOrientationGroups(false, this.activeClassId, 'ΟΠ');
      this.OrientationGroupSub = this._ngRedux.select("orientationGroup")
          .map(orientationGroup => <IOrientationGroupRecords>orientationGroup)
          .subscribe(ogs => {
                this.listsize = 0;
                this.activeChoice = -1;
                ogs.reduce(({}, orientationgroup) => {
                  ++this.listsize;
                  if (orientationgroup.get("selected") === true) {
                      this.activeChoice = orientationgroup.get("id") -1;
                  }
                  return orientationgroup;
                }, {});
                this.OrientationGroup$.next(ogs);
          }, error => { console.log("error selecting orientation"); });
    }


    navigateNext() {
        if (this.activeChoice == -1) {
          this.modalTitle.next("Δεν επιλέχθηκε Ομάδα Προσανατολισμού");
          this.modalText.next("Παρακαλούμε να επιλέξετε πρώτα μία Ομάδα Προσανατολισμού");
          this.modalHeader.next("modal-header-danger");
          this.showModal();
        }
        else  {
          if (this.activeClassId == 3)
            this.router.navigate(["/electivecourse-fields-select"]);
          else
            this.router.navigate(["/gelstudent-application-form-main"]);
        }
    }

    /*
    private saveSelected(ind: number): void {
      console.log("Test value:");
      console.log(this.activechoice);
      console.log(ind);
      if (this.activechoice == ind)
            return;

      this._ogs.saveOrientationGroupSelected(this.activechoice, ind);
      this.activechoice = ind;

    }
    */


    private saveSelected(ind: number , sel: number): void {
        for (let i=0; i<this.listsize; i++)
            this._ogs.saveOrientationGroupSelected(i,1);

        this._ogs.saveOrientationGroupSelected(ind,sel);
    }



}
