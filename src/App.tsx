import { useState } from "react";
import { Switch, Route, Router as WouterRouter } from "wouter";
import "./welthflow.css";
import SecurityGateway from "./SecurityGateway";
import Home from "./pages/home";
import FAQ from "./pages/faq";
import About from "./pages/about";
import Terms from "./pages/terms";
import Investment from "./pages/investment";
import Loan from "./pages/loan";
import Forex from "./pages/forex";
import Affiliates from "./pages/affiliates";
import Admin from "./pages/admin";
import NotFound from "./pages/not-found";

function Router() {
  return (
    <Switch>
      <Route path="/" component={Home} />
      <Route path="/faq" component={FAQ} />
      <Route path="/about" component={About} />
      <Route path="/terms" component={Terms} />
      <Route path="/investment" component={Investment} />
      <Route path="/loan" component={Loan} />
      <Route path="/forex" component={Forex} />
      <Route path="/affiliates" component={Affiliates} />
      <Route path="/admin-secure-9x7" component={Admin} />
      <Route component={NotFound} />
    </Switch>
  );
}

export default function App() {
  const [verified, setVerified] = useState(false);

  return (
    <>
      {!verified && <SecurityGateway onVerified={() => setVerified(true)} />}
      <div style={{ visibility: verified ? "visible" : "hidden", opacity: verified ? 1 : 0, transition: "opacity 0.5s ease" }}>
        <WouterRouter base={import.meta.env.BASE_URL.replace(/\/$/, "")}>
          <Router />
        </WouterRouter>
      </div>
    </>
  );
}
