import * as React from "react";
import {
  Body,
  Container,
  Head,
  Html,
  Preview,
  Tailwind,
} from "@react-email/components";

type BaseEmailTemplateProps = {
  preview: string;
} & React.PropsWithChildren;

export const BaseEmailTemplate = ({
  preview,
  children,
}: BaseEmailTemplateProps) => {
  return (
    <Html>
      <Head></Head>
      <Tailwind>
        <Preview>{preview}</Preview>
        <Body className='mx-auto my-auto bg-gray-400 p-10 px-2 font-sans'>
          <Container className='mx-auto max-w-[340px] rounded-[20px] border border-solid border-gray-600 bg-white p-6'>
            {children}
          </Container>
        </Body>
      </Tailwind>
    </Html>
  );
};
