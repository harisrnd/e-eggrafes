import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";

import {
    REGIONSCHOOLS_INIT,
    REGIONSCHOOLS_ORDER_SAVE,
    REGIONSCHOOLS_RECEIVED,
    REGIONSCHOOLS_SELECTED_SAVE,
    REGIONSCHOOLS_SELECTED_SAVE_WITHIDS,
} from "../constants";
import { HelperDataService } from "../services/helper-data-service";
import { IAppState } from "../store";

@Injectable()
export class RegionSchoolsActions {
    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService) { }

    getRegionSchools = (classActive, courseActive, editApp, reload) => {
        const { regions } = this._ngRedux.getState();
        if (reload === true || (reload === false && regions.size === 0)) {
            return this._hds.getRegionsWithSchools(classActive, courseActive, editApp).then(regions => {
                return this._ngRedux.dispatch({
                    type: REGIONSCHOOLS_RECEIVED,
                    payload: {
                        regions
                    }
                });
            });
        }
        //else
        //  return;
    };

    initRegionSchools = () => {
        return this._ngRedux.dispatch({
            type: REGIONSCHOOLS_INIT,
            payload: {
            }
        });
    };

    saveRegionSchoolsSelected = (checked, i, j, orderId: number) => {
        return this._ngRedux.dispatch({
            type: REGIONSCHOOLS_SELECTED_SAVE,
            payload: {
                checked: checked,
                rIndex: i,
                sIndex: j,
                orderId: orderId
            }
        });
    };

    saveRegionSchoolsSelectedwithIds = (checked, school_id, orderId: number) => {
        return this._ngRedux.dispatch({
            type: REGIONSCHOOLS_SELECTED_SAVE_WITHIDS,
            payload: {
                checked: checked,
                school_id: school_id,
                orderId: orderId
            }
        });
    };

    saveRegionSchoolsOrder = (selectedSchools) => {
        return this._ngRedux.dispatch({
            type: REGIONSCHOOLS_ORDER_SAVE,
            payload: {
                selectedSchools
            }
        });
    };

}
