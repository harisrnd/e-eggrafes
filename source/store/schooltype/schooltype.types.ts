import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface ISchoolType {
    id: number;
    name: string;
}

export interface ISchoolTypeRecord extends TypedRecord<ISchoolTypeRecord>, ISchoolType { };
export type ISchoolTypeRecords = List<ISchoolTypeRecord>;