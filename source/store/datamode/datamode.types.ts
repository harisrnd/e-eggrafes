import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IDataMode {
    app_update: boolean;
    appid: string;
    apptype: string;

    currentclass: string;
    sector_id: string;
    course_id: string;
    epal_choice: string;
}

export interface IDataModeRecord extends TypedRecord<IDataModeRecord>, IDataMode { };
export type IDataModeRecords = List<IDataModeRecord>;
