import { Component, Injectable, OnInit } from "@angular/core";
import { Router } from "@angular/router";

@Component({ 
    selector: "school-type-selection",
    template: `
           <div class="row" style="margin-top: 130px; margin-bottom: 200px;">
               <div class="col-md-3 offset-md-3">
                <button type="submit" class="btn-primary btn-lg btn-block isclickable" style="margin: 0px; font-size: 1em; padding: 5px;" (click)="navigatepdegel()">
                Γενικά <br />Λύκεια
                </button>
                </div>
                <div class="col-md-6">
                 <button type="submit" class="btn-primary btn-lg btn-block isclickable" style="margin: 0px; font-size: 1em; padding: 5px;" (click)="navigatepdeepal()">
                Επαγγελματικά <br />Λύκεια
                </button>
               </div>
            </div>
  `
})

@Injectable() export default class SchoolTypeSelectionPde implements OnInit {

    constructor(
        private router: Router,
    ) {
    };

    ngOnInit() {

    }

    navigatepdeepal() {
        this.router.navigate(["/school/perfecture-view"]);

    }

    navigatepdegel() {
        this.router.navigate(["/school/gelperfecture-view"]);
    }

}
