import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";
import { SCHOOLTYPE_SAVE } from "../constants";
import { SCHOOLTYPE_INIT, SCHOOLTYPE_RECEIVED, SCHOOLTYPE_RESET } from "../constants";
import { IAppState } from "../store";

import { HelperDataService } from "../services/helper-data-service";

@Injectable()
export class SchoolTypeActions {
    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService) { }

    initSchoolType = () => {
            return this._ngRedux.dispatch({
                type: SCHOOLTYPE_INIT,
                payload: {
                }
            });
        };

    saveSchoolTypeSelected = (schooltype_id, schooltype_name) => {
        return this._ngRedux.dispatch({
            type: SCHOOLTYPE_SAVE,
            payload: {
                schooltype_id,
                schooltype_name
            }
        });
    };



}
