import * as React from "react";

import { BaseEmailTemplate, EmailText } from "./_components";

export default function ExampleEmail() {
  return (
    <BaseEmailTemplate preview='This is an example email'>
      <EmailText size='title'>Example Email</EmailText>
      <EmailText className='my-4'>
        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Possimus
        corrupti incidunt eius deserunt ullam? Rem laborum beatae porro earum
        suscipit doloremque obcaecati maxime atque fugiat placeat itaque magni,
        aut veritatis consequatur eum, voluptates quas quod ipsam temporibus
        numquam nam quam.
      </EmailText>
      <EmailText>
        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Possimus
        corrupti incidunt eius deserunt ullam? Rem laborum beatae porro earum
        suscipit doloremque obcaecati maxime atque fugiat placeat itaque magni,
        aut veritatis consequatur eum, voluptates quas quod ipsam temporibus
        numquam nam quam.
      </EmailText>
    </BaseEmailTemplate>
  );
}
