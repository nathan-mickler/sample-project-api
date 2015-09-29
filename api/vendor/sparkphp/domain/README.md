# Domain and Payload Interfaces

Interfaces to support the separation of concerns between domain logic and output
encapsulation. In this pattern, the Domain object will always return a Payload
object that contains the output of the Domain. Can be used in any context.

The Payload should be implemented as an immutable object, creating a copy whenever
the status, output, or messages are modified.
