import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";
import { GELCLASSES_SAVE } from "../constants";
import { GELCLASSES_INIT, GELCLASSES_RECEIVED, GELCLASSES_RESET, GELCLASSES_SAVE_WITHIDS } from "../constants";
import { IAppState } from "../store";

import { HelperDataService } from "../services/helper-data-service";

@Injectable()
export class GelClassesActions {
    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService) { }

    initGelClasses = () => {
            return this._ngRedux.dispatch({
                type: GELCLASSES_INIT,
                payload: {
                }
            });
        };

    getClassesList = (reload) => {
        const { gelclasses } = this._ngRedux.getState();
        if (reload === true || (reload === false && gelclasses.size === 0)) {
            return this._hds.getClassesList().then(gelclasses => {
                return this._ngRedux.dispatch({
                    type: GELCLASSES_RECEIVED,
                    payload: {
                        gelclasses
                    }
                });
            });
        }
    };

    saveGelClassesSelected = (selected_id:number, new_selected_choice_id:number) => {
        return this._ngRedux.dispatch({
            type: GELCLASSES_SAVE,
            payload: {
                selected_id:selected_id,
                new_selected_choice_id: new_selected_choice_id
            }
        });
    };

    saveGelClassesSelectedwithIds = (selected_id:number, new_selected_choice_id:number) => {
        return this._ngRedux.dispatch({
            type: GELCLASSES_SAVE_WITHIDS,
            payload: {
                selected_id:selected_id,
                new_selected_choice_id: new_selected_choice_id
            }
        });
    };


    resetGelClassesSelected = () => {
        const { gelclasses } = this._ngRedux.getState();
        return this._hds.getClassesList().then(gelclasses => {
            return this._ngRedux.dispatch({
                    type: GELCLASSES_RESET,
                    payload: {
                        gelclasses
                    }
                });
            });
    };

}
