import { Component, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";

import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { NgRedux } from "@angular-redux/store";
import { IAppState } from "../../store/store";
import { IDataModeRecords } from "../../store/datamode/datamode.types";
import { DataModeActions } from "../../actions/datamode.actions";

@Component({
    selector: "breadcrumbs",
    template: `
          <div [hidden]="currentUrl !== '/epal-class-select'" class="col-sm-12"><p class="crumb" >{{appStatusTxt}} -> Επιλογή Τάξης </p></div>
          <div [hidden]="currentUrl !== '/sector-fields-select'" class="col-sm-12"><p class="crumb" >{{appStatusTxt}} ->  Επιλογή Τoμέα</p></div>
          <div [hidden]="currentUrl !== '/region-schools-select'" class="col-sm-12"><p class="crumb" >{{appStatusTxt}} ->  Επιλογή Σχολείου ανα Περιφερειακή Διεύθυνση</p></div>
          <div [hidden]="currentUrl !== '/sectorcourses-fields-select'" class="col-sm-12"><p class="crumb" >{{appStatusTxt}} ->  Επιλογή Ειδικότητας ανα τoμέα</p></div>
          <div [hidden]="currentUrl !== '/schools-order-select'" class="col-sm-12"><p class="crumb" >{{appStatusTxt}} ->  Σειρά Προτίμησης Επιλεχθέντων Σχολείων</p></div>
          <div [hidden]="currentUrl !== '/student-application-form-main'" class="col-sm-12"><p class="crumb" >{{appStatusTxt}} ->  Προσωπικά Στοιχεία</p></div>
          <div [hidden]="currentUrl !== '/application-submit'" class="col-sm-12"><p class="crumb" >{{appStatusTxt}} ->  Προεπισκόπηση Δήλωσης Προτίμησης</p></div>
          <div [hidden]="currentUrl !== '/submited-preview'" class="col-sm-12"><p class="crumb" > Υποβληθείσες Δηλώσεις Προτίμησης</p></div>
          <div [hidden]="currentUrl !== '/ministry'" class="col-sm-12"><p class="crumb" > Διαχειριστής Υπουργείου Παιδείας -> Σύνδεση</p></div>
  `
})

@Injectable() export default class Breadcrumbs implements OnInit {
    private currentUrl: string;
    private datamodeSub: Subscription;
    private datamode$: BehaviorSubject<IDataModeRecords>;
    private appStatusTxt: string;

    constructor(private _router: Router,
                private _cfd: DataModeActions,
                private _ngRedux: NgRedux<IAppState>) { }
    ngOnInit() {
        this.currentUrl = this._router.url;

        this.appStatusTxt = "Νέα Δήλωση Προτίμησης";
        this.datamodeSub = this._ngRedux.select("datamode")
            .map(datamode => <IDataModeRecords>datamode)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                    ecs.reduce(({}, datamode,i) => {
                        if (datamode.get("app_update") === true) {
                            this.appStatusTxt = "Τροποποίηση Δήλωσης Προτίμησης";
                        }
                        return datamode;
                    }, {});
                } else {

                }
            }, error => { console.log("error selecting datamode"); });
    }

    ngOnDestroy() {
        if (this.datamodeSub)
          this.datamodeSub.unsubscribe();
    }

}
