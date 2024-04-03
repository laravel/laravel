import type { ModalProps } from "@/shared.types";
import { Modal } from "@/ui";

export const ExampleModal = ({ show, onClose }: ModalProps) => {
  return (
    <Modal
      show={show}
      title="Example modal"
      description="Lorem ipsum dolor sit consectetur ipsum dolor. Lorem ipsum dolor sit consectetur ipsum."
      onClose={onClose}
    />
  );
};
