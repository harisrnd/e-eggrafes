import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";

import { ELECTIVECOURSEFIELDS_INIT, ELECTIVECOURSEFIELDS_RECEIVED, ELECTIVECOURSEFIELDS_SELECTED_SAVE, ELECTIVECOURSES_ORDER_SAVE } from "../constants";
import { HelperDataService } from "../services/helper-data-service";
import { IAppState } from "../store";

@Injectable()
export class ElectiveCourseFieldsActions {
    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService) { }

    getElectiveCourseFields = (reload, activeClassId) => {
        const { electivecourseFields } = this._ngRedux.getState();

        if (reload === true || (reload === false && electivecourseFields.size === 0)) {
            return this._hds.getElectiveCourseFields(activeClassId).then(electivecourseFields => {
                return this._ngRedux.dispatch({
                    type: ELECTIVECOURSEFIELDS_RECEIVED,
                    payload: {
                        electivecourseFields
                    }
                });
            });
        }
    };

    initElectiveCourseFields = () => {
        return this._ngRedux.dispatch({
            type: ELECTIVECOURSEFIELDS_INIT,
            payload: {
            }
        });
    };


    saveElectiveCourseFieldsSelected = (newChoice: number, isSelected: number, orderId: number) => {
        return this._ngRedux.dispatch({
            type: ELECTIVECOURSEFIELDS_SELECTED_SAVE,
            payload: {
                newChoice: newChoice,
                isSelected: isSelected,
                orderId: orderId,
            }
        });
    };

    saveCoursesOrder = (selectedCourses) => {
        return this._ngRedux.dispatch({
            type: ELECTIVECOURSES_ORDER_SAVE,
            payload: {
                selectedCourses
            }
        });
    };

}
