import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";

import { DATAMODE_SAVE } from "../constants";
import { DATAMODE_INIT } from "../constants";
import { IAppState } from "../store";

@Injectable()
export class DataModeActions {
    constructor(
        private _ngRedux: NgRedux<IAppState>) { }

    saveDataModeSelected = (dataMode) => {
        return this._ngRedux.dispatch({
            type: DATAMODE_SAVE,
            payload: {
                dataMode
            }
        });
    };

    initDataMode = () => {
        return this._ngRedux.dispatch({
            type: DATAMODE_INIT,
            payload: {
            }
        });
    };

}
