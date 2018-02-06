import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";

import { GELSTUDENTDATAFIELDS_INIT, GELSTUDENTDATAFIELDS_SAVE } from "../constants";
import { IAppState } from "../store";

@Injectable()
export class GelStudentDataFieldsActions {
    constructor(
        private _ngRedux: NgRedux<IAppState>) { }

    saveGelStudentDataFields = (gelstudentDataFields) => {

        return this._ngRedux.dispatch({
            type: GELSTUDENTDATAFIELDS_SAVE,
            payload: {
                gelstudentDataFields
            }
        });

    };

    initGelStudentDataFields = () => {
        return this._ngRedux.dispatch({
            type: GELSTUDENTDATAFIELDS_INIT,
            payload: {
            }
        });
    };

}
