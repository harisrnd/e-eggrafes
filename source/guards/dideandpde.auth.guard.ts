import { Injectable } from "@angular/core";
import { CanActivate } from "@angular/router";
import { Router } from "@angular/router";

import { DIDE_ROLE } from "../constants";
import { PDE_ROLE } from "../constants";

import { AuthService } from "../services/auth.service";

@Injectable()
export default class DidepdeAuthGuard implements CanActivate {

    constructor(private authService: AuthService, private router: Router) { }

    canActivate() {
        return this.authService.isLoggedInDoubleRole(DIDE_ROLE, PDE_ROLE).then(loggedIn => {
            if (!loggedIn) {
                this.router.navigate(["/school/logout"]);
            }
            return loggedIn;
        }).catch(err => {
            return false;
        });
    }
}
 