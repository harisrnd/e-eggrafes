import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";

import { ORIENTATIONGROUP_SAVE,ORIENTATIONGROUP_RECEIVED } from "../constants";
import { ORIENTATIONGROUP_INIT } from "../constants";
import { HelperDataService } from "../services/helper-data-service";
import { IAppState } from "../store";

@Injectable()
export class OrientationGroupActions {
   constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService) { }

    getOrientationGroups = (reload, classid, typeid) => {
        const { orientationGroup } = this._ngRedux.getState();
        if (reload === true || (reload === false && orientationGroup.size === 0)) {
            return this._hds.getOrientationGroup(classid, typeid).then(orientat => {
                return this._ngRedux.dispatch({
                    type: ORIENTATIONGROUP_RECEIVED,
                    payload: {
                        orientat
                    }
                });
            });
        }
    };


    saveOrientationGroupSelected = (prevChoice: number, newChoice: number) => {
        return this._ngRedux.dispatch({
            type: ORIENTATIONGROUP_SAVE,
            payload: {
                prevChoice: prevChoice,
                newChoice: newChoice
            }
        });
    };


   
    initOrientationGroup = () => {
        return this._ngRedux.dispatch({
            type: ORIENTATIONGROUP_INIT,
            payload: {
            }
        });
    };

}
