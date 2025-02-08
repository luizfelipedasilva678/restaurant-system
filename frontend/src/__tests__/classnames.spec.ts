import { describe, expect, it } from "vitest";
import { classnames } from "../lib/classnames";

describe("classnames", () => {
	it("should build the class string correctly based on the record param", () => {
		const classname = classnames({
			test: true,
			"test-one": true,
			"test-two": false,
		});

		expect(classname).toBe("test test-one");
	});

	it("should concat record classes with default classes", () => {
		const classname = classnames(
			{
				test: true,
				"test-one": true,
				"test-two": false,
			},
			"default",
			"default-two",
			"default-three",
		);

		expect(classname).toBe("test test-one default default-two default-three");
	});
});
