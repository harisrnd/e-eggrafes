import { Injectable } from "@angular/core";
import { CanActivate } from "@angular/router";
import { Router } from "@angular/router";

import { SCHOOLGYM_ROLE,SCHOOLGYMLT_ROLE } from "../constants";
import { AuthService } from "../services/auth.service";

@Injectable()
export default class SchoolGymStudentsLockedGuard implements CanActivate {

    constructor(private authService: AuthService, private router: Router) { }

    canActivate() {
        return this.authService.isGelStudentsLockedforTworoles(SCHOOLGYM_ROLE,SCHOOLGYMLT_ROLE).then(isLocked => {
            if (isLocked) {
                this.router.navigate(["/school"]);
                return false;
            } else
                return true;
        }).catch(err => {
            return false;
        });
    }
}
