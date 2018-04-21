import "./globalstyles.css";

import { DevToolsExtension, NgRedux } from "@angular-redux/store";
import { Component } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";

import { IAppState, rootReducer } from "../store";

@Component({
    selector: "main",
    template: `
  <reg-header></reg-header>
  <reg-navbar></reg-navbar>
  <reg-main></reg-main>
  <reg-footer></reg-footer>
  `
})
export default class Main {
    private path: string = "";
    private pathSchool: string = "school";

    constructor(
        private router: Router,
        private activatedRoute: ActivatedRoute,
        private _ngRedux: NgRedux<IAppState>,
        private _devTools: DevToolsExtension
    ) {


        const tools = _devTools.enhancer({
            // deserializeState: reimmutify,
        });

        //make storeEnhancers = [] in order for PRODUCTION MODE
        const storeEnhancers =
          //_devTools.isEnabled() ?
          //[ _devTools.enhancer() ] :
          [];

        _ngRedux.configureStore(
            rootReducer,
            {},
            [],
            // _devTools.enhancer(),
            storeEnhancers);

            // middleware,
            // tools ? [ ...enhancers, tools ] : enhancers);
            // tools);
      // );

    }
}
