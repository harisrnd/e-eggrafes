import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";

import { LANGCOURSEFIELDS_INIT, LANGCOURSEFIELDS_RECEIVED, LANGCOURSEFIELDS_SELECTED_SAVE, LANGCOURSES_ORDER_SAVE, LANGCOURSEFIELDS_SELECTED_SAVE_WITHIDS } from "../constants";
import { HelperDataService } from "../services/helper-data-service";
import { IAppState } from "../store";

@Injectable()
export class LangCourseFieldsActions {
    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService) { }

    getLangCourseFields = (reload, activeClassId) => {
        const { langcourseFields } = this._ngRedux.getState();

        if (reload === true || (reload === false && langcourseFields.size === 0)) {
            return this._hds.getLangCourseFields(activeClassId).then(langcourseFields => {
                return this._ngRedux.dispatch({
                    type: LANGCOURSEFIELDS_RECEIVED,
                    payload: {
                        langcourseFields
                    }
                });
            });
        }
    };

    initLangCourseFields = () => {
        return this._ngRedux.dispatch({
            type: LANGCOURSEFIELDS_INIT,
            payload: {
            }
        });
    };


    saveLangCourseFieldsSelected = (newChoice: number, isSelected: number, orderId: number) => {
        return this._ngRedux.dispatch({
            type: LANGCOURSEFIELDS_SELECTED_SAVE,
            payload: {
                newChoice: newChoice,
                isSelected: isSelected,
                orderId: orderId,
            }
        });
    };

    saveLangCourseFieldsSelectedwithIds = (newChoice: number, isSelected: number, orderId: number) => {
        return this._ngRedux.dispatch({
            type: LANGCOURSEFIELDS_SELECTED_SAVE_WITHIDS,
            payload: {
                newChoice: newChoice,
                isSelected: isSelected,
                orderId: orderId,
            }
        });
    };

    saveLangsOrder = (selectedCourses) => {
        return this._ngRedux.dispatch({
            type: LANGCOURSES_ORDER_SAVE,
            payload: {
                selectedCourses
            }
        });
    };

}
