import { Injectable } from "@angular/core";
import { CanActivate } from "@angular/router";
import { Router } from "@angular/router";

import { STUDENT_ROLE } from "../constants";
import { AuthService } from "../services/auth.service";

@Injectable()
export default class StudentGelLockGuard implements CanActivate {

    constructor(private authService: AuthService, private router: Router) { }

    canActivate() {
        return this.authService.isGelApplicationLocked(STUDENT_ROLE).then(isLocked => {
            if (isLocked) {
                this.router.navigate([""]);
                return false;
            } else
                return true;
        }).catch(err => {
            console.log("exception");
            return false;
        });
    }
}
