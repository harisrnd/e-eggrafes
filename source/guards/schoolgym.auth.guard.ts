import { Injectable } from "@angular/core";
import { CanActivate } from "@angular/router";
import { Router } from "@angular/router";

import { SCHOOLGYM_ROLE, SCHOOLGYMLT_ROLE } from "../constants";
import { AuthService } from "../services/auth.service";

@Injectable()
export default class SchoolGymAuthGuard implements CanActivate {

    constructor(private authService: AuthService, private router: Router) { }

    canActivate() {
        return this.authService.isLoggedInFortworoles(SCHOOLGYM_ROLE, SCHOOLGYMLT_ROLE).then(loggedIn => {
            if (!loggedIn) {
                this.router.navigate(["/school/logout"]);
            }
            return loggedIn;
        }).catch(err => {
            return false;
        });
    }
}
